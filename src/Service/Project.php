<?php
/*
 * This file is part of the Fusio Plant project (https://github.com/apioo/fusio-plant).
 * Fusio Plant is a server control panel to easily self-host apps on your server.
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Service;

use App\Exception\ConfigurationException;
use App\Exception\PortResolveException;
use App\Exception\ProcessTimeoutException;
use App\Model;
use App\Model\Message;
use App\Service\Project\ProjectExecutor;
use App\Service\System\SystemExecutor;
use App\Table;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\DispatcherInterface;
use Fusio\Impl\Service\System\FrameworkConfig;
use PSX\CloudEvents\Builder;
use PSX\DateTime\LocalDateTime;
use PSX\Http\Exception as StatusCode;
use PSX\Json\Parser;
use Ramsey\Uuid\Uuid;

readonly class Project
{
    public function __construct(
        private Table\Project $projectTable,
        private Table\Monitor $monitorTable,
        private ProjectExecutor $projectExecutor,
        private SystemExecutor $systemExecutor,
        private JsonParser $jsonParser,
        private FrameworkConfig $frameworkConfig,
        private DispatcherInterface $dispatcher
    ) {
    }

    public function create(Model\Project $project, ContextInterface $context): Message
    {
        $this->assertProject($project);

        $this->projectTable->beginTransaction();

        try {
            $row = new Table\Generated\ProjectRow();
            $row->setUserId($context->getUser()->getId());
            $row->setDisplayId(Uuid::uuid4()->toString());
            $row->setName($project->getName());
            $row->setApps(Parser::encode($project->getApps()));
            $row->setUpdateDate(LocalDateTime::now());
            $row->setInsertDate(LocalDateTime::now());
            $this->projectTable->create($row);

            try {
                $output = $this->projectExecutor->setup($this->projectTable->getLastInsertId(), $project);
            } catch (ProcessTimeoutException|ConfigurationException|PortResolveException $e) {
                throw new StatusCode\InternalServerErrorException('Could not setup project, got: ' . $e->getMessage(), previous: $e);
            }

            foreach ($this->getDomains($project) as $domain) {
                try {
                    $request = new Model\CertbotRequest();
                    $request->setDomain($domain);
                    $request->setEmail($this->frameworkConfig->getMailSender());
                    $output.= $this->systemExecutor->certbot($request);
                } catch (ProcessTimeoutException $e) {
                    throw new StatusCode\InternalServerErrorException('Could not register SSL certificate for domain ' . $domain . ', got: ' . $e->getMessage(), previous: $e);
                }
            }

            $this->dispatchEvent('project.created', $row, $row->getDisplayId());

            $this->projectTable->commit();
        } catch (\Throwable $e) {
            $this->projectTable->rollBack();

            throw $e;
        }

        return $this->newMessage('Project successfully created', $row->getDisplayId(), $output);
    }

    public function update(string $id, Model\Project $project): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        // it is not possible to change the project name
        $project->setName($row->getName());

        $this->assertProject($project);

        $this->projectTable->beginTransaction();

        try {
            $row->setApps(Parser::encode($project->getApps()));
            $row->setUpdateDate(LocalDateTime::now());
            $this->projectTable->update($row);

            try {
                $output = $this->projectExecutor->setup($row->getId(), $project);
            } catch (ProcessTimeoutException $e) {
                throw new StatusCode\InternalServerErrorException('Could not update project, got: ' . $e->getMessage(), previous: $e);
            }

            foreach ($this->getDomains($project) as $domain) {
                try {
                    $request = new Model\CertbotRequest();
                    $request->setDomain($domain);
                    $request->setEmail($this->frameworkConfig->getMailSender());
                    $output.= $this->systemExecutor->certbot($request);
                } catch (ProcessTimeoutException $e) {
                    throw new StatusCode\InternalServerErrorException('Could not register SSL certificate for domain ' . $domain . ', got: ' . $e->getMessage(), previous: $e);
                }
            }

            $this->dispatchEvent('project.updated', $row, $row->getDisplayId());

            $this->projectTable->commit();
        } catch (\Throwable $e) {
            $this->projectTable->rollBack();

            throw $e;
        }

        return $this->newMessage('Project successfully updated', $row->getDisplayId(), $output);
    }

    public function delete(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        $this->projectTable->beginTransaction();

        try {
            $this->monitorTable->deleteByProjectId($row->getId());

            $this->projectTable->delete($row);

            try {
                $output = $this->projectExecutor->remove($row->getId(), $row);
            } catch (ProcessTimeoutException $e) {
                throw new StatusCode\InternalServerErrorException('Could not remove project, got: ' . $e->getMessage(), previous: $e);
            }

            $this->dispatchEvent('project.deleted', $row, $row->getDisplayId());

            $this->projectTable->commit();
        } catch (\Throwable $e) {
            $this->projectTable->rollBack();

            throw $e;
        }

        return $this->newMessage('Project successfully deleted', $row->getDisplayId(), $output);
    }

    public function deploy(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->projectExecutor->deploy($row->getId(), $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not deploy, got: ' . $e->getMessage(), previous: $e);
        }

        $this->dispatchEvent('project.deploy', $row, $row->getDisplayId());

        return $this->newMessage('Project deploy successfully executed', $row->getDisplayId(), $output);
    }

    public function down(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->projectExecutor->down($row->getId(), $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not down, got: ' . $e->getMessage(), previous: $e);
        }

        $this->dispatchEvent('project.down', $row, $row->getDisplayId());

        return $this->newMessage('Project down successfully executed', $row->getDisplayId(), $output);
    }

    public function logs(string $id): Model\DockerLogs
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->projectExecutor->logs($row->getId(), $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not get logs, got: ' . $e->getMessage(), previous: $e);
        }

        $logs = new Model\DockerLogs();
        $logs->setOutput($output);
        return $logs;
    }

    public function ps(string $id): Model\DockerProcesses
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->projectExecutor->ps($row->getId(), $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not get ps, got: ' . $e->getMessage(), previous: $e);
        }

        $lines = $this->jsonParser->parseLines($output, Model\DockerProcess::class);

        $collection = new Model\DockerProcesses();
        $collection->setTotalResults(count($lines));
        $collection->setEntry($lines);
        return $collection;
    }

    public function pull(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->projectExecutor->pull($row->getId(), $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not pull, got: ' . $e->getMessage(), previous: $e);
        }

        $this->dispatchEvent('project.pull', $row, $row->getDisplayId());

        return $this->newMessage('Project pull successfully executed', $row->getDisplayId(), $output);
    }

    public function stats(string $id): Model\DockerStatistics
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->projectExecutor->stats($row->getId(), $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not get stats, got: ' . $e->getMessage(), previous: $e);
        }

        $lines = $this->jsonParser->parseLines($output, Model\DockerStatistic::class);

        $collection = new Model\DockerStatistics();
        $collection->setTotalResults(count($lines));
        $collection->setEntry($lines);
        return $collection;
    }

    public function up(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->projectExecutor->up($row->getId(), $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not up, got: ' . $e->getMessage(), previous: $e);
        }

        $this->dispatchEvent('project.up', $row, $row->getDisplayId());

        return $this->newMessage('Project up successfully executed', $row->getDisplayId(), $output);
    }

    private function dispatchEvent(string $type, Table\Generated\ProjectRow $data, string $id): void
    {
        $event = (new Builder())
            ->withId(Uuid::uuid4()->toString())
            ->withSource('/project/' . $id)
            ->withType($type)
            ->withDataContentType('application/json')
            ->withData($data)
            ->build();

        $this->dispatcher->dispatch($type, $event);
    }

    private function assertProject(Model\Project $project): void
    {
        $name = $project->getName();
        if (empty($name) || !preg_match('/^[a-zA-Z0-9\\-]{3,128}$/', $name)) {
            throw new StatusCode\BadRequestException('Project name must contain only alphanumerical characters or hyphens and must be between 3 and 128 characters');
        }

        $apps = $project->getApps();
        if ($apps === null || count($apps) === 0) {
            throw new StatusCode\BadRequestException('No apps provided');
        }

        foreach ($apps as $app) {
            if (!ctype_alnum($app->getName())) {
                throw new StatusCode\BadRequestException('App name must contain only alphanumerical characters');
            }
        }
    }

    private function newMessage(string $message, string $id, ?string $output = null): Message
    {
        $return = new Message();
        $return->setSuccess(true);
        $return->setMessage($message);
        $return->setId($id);
        $return->setOutput($output);
        return $return;
    }

    private function getDomains(Model\Project $project): \Generator
    {
        $apps = $project->getApps() ?? [];
        foreach ($apps as $app) {
            $domains = $app->getDomains() ?? [];
            foreach ($domains as $domain) {
                yield $domain;
            }
        }
    }
}
