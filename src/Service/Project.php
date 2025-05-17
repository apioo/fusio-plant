<?php
/*
 * This file is part of the Fusio Plant project (https://fusio-project.org/product/plant).
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
use App\Service\Project\Worker;
use App\Table;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\DispatcherInterface;
use PSX\CloudEvents\Builder;
use PSX\DateTime\LocalDateTime;
use PSX\Http\Exception as StatusCode;
use PSX\Json\Parser;
use Ramsey\Uuid\Uuid;

readonly class Project
{
    public function __construct(private Table\Project $projectTable, private Worker $worker, private DispatcherInterface $dispatcher)
    {
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
                $output = $this->worker->setup($this->projectTable->getLastInsertId(), $project);
            } catch (ProcessTimeoutException|ConfigurationException|PortResolveException $e) {
                throw new StatusCode\InternalServerErrorException('Could not setup project, got: ' . $e->getMessage(), previous: $e);
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

        $this->assertProject($project);

        $this->projectTable->beginTransaction();

        try {
            $row->setApps(Parser::encode($project->getApps()));
            $row->setUpdateDate(LocalDateTime::now());
            $this->projectTable->update($row);

            try {
                $output = $this->worker->setup($row->getId(), $project);
            } catch (ProcessTimeoutException $e) {
                throw new StatusCode\InternalServerErrorException('Could not update project, got: ' . $e->getMessage(), previous: $e);
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
            $this->projectTable->delete($row);

            try {
                $output = $this->worker->remove($row->getId(), $row);
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

    public function certbot(string $id, Model\ProjectCertbot $certbot): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->worker->certbot($id, $certbot);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not obtain SSL certificates, got: ' . $e->getMessage(), previous: $e);
        }

        $this->dispatchEvent('project.certbot', $row, $row->getDisplayId());

        return $this->newMessage('Project certbot successfully executed', $row->getDisplayId(), $output);
    }

    public function pull(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->worker->pull($id, $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not pull, got: ' . $e->getMessage(), previous: $e);
        }

        $this->dispatchEvent('project.pull', $row, $row->getDisplayId());

        return $this->newMessage('Project pull successfully executed', $row->getDisplayId(), $output);
    }

    public function up(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->worker->up($id, $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not up, got: ' . $e->getMessage(), previous: $e);
        }

        $this->dispatchEvent('project.up', $row, $row->getDisplayId());

        return $this->newMessage('Project up successfully executed', $row->getDisplayId(), $output);
    }

    public function down(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->worker->down($id, $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not down, got: ' . $e->getMessage(), previous: $e);
        }

        $this->dispatchEvent('project.down', $row, $row->getDisplayId());

        return $this->newMessage('Project up successfully executed', $row->getDisplayId(), $output);
    }

    public function logs(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->worker->logs($id, $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not get logs, got: ' . $e->getMessage(), previous: $e);
        }

        return $this->newMessage('Project logs successfully executed', $row->getDisplayId(), $output);
    }

    public function ps(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->worker->ps($id, $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not get ps, got: ' . $e->getMessage(), previous: $e);
        }

        return $this->newMessage('Project ps successfully executed', $row->getDisplayId(), $output);
    }

    public function stats(string $id): Message
    {
        $row = $this->projectTable->findOneByDisplayId($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        try {
            $output = $this->worker->stats($id, $row);
        } catch (ProcessTimeoutException $e) {
            throw new StatusCode\InternalServerErrorException('Could not get stats, got: ' . $e->getMessage(), previous: $e);
        }

        return $this->newMessage('Project stats successfully executed', $row->getDisplayId(), $output);
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
        if (!ctype_alnum($project->getName())) {
            throw new StatusCode\BadRequestException('Project name must contain only alphanumerical characters');
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
}
