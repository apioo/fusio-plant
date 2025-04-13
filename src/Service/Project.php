<?php

namespace App\Service;

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
            $row->setApps(Parser::encode($project->getApps()));
            $row->setUpdateDate(LocalDateTime::now());
            $row->setInsertDate(LocalDateTime::now());
            $this->projectTable->create($row);

            $output = $this->worker->setup($this->projectTable->getLastInsertId(), $project);

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
        $row = $this->projectTable->find($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        $this->assertProject($project);

        $this->projectTable->beginTransaction();

        try {
            $row->setApps(Parser::encode($project->getApps()));
            $row->setUpdateDate(LocalDateTime::now());
            $this->projectTable->update($row);

            $output = $this->worker->setup($row->getId(), $project);

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
        $row = $this->projectTable->find($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        $this->projectTable->beginTransaction();

        try {
            $this->projectTable->delete($row);

            $output = $this->worker->remove($row->getId(), $row);

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
        $row = $this->projectTable->find($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        $output = $this->worker->certbot($id, $certbot);

        return $this->newMessage('Project certbot successfully executed', $row->getDisplayId(), $output);
    }

    public function pull(string $id): Message
    {
        $row = $this->projectTable->find($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        $output = $this->worker->pull($id, $row);

        return $this->newMessage('Project pull successfully executed', $row->getDisplayId(), $output);
    }

    public function up(string $id): Message
    {
        $row = $this->projectTable->find($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        $output = $this->worker->up($id, $row);

        return $this->newMessage('Project up successfully executed', $row->getDisplayId(), $output);
    }

    public function down(string $id): Message
    {
        $row = $this->projectTable->find($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        $output = $this->worker->down($id, $row);

        return $this->newMessage('Project up successfully executed', $row->getDisplayId(), $output);
    }

    public function logs(string $id): Message
    {
        $row = $this->projectTable->find($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        $output = $this->worker->logs($id, $row);

        return $this->newMessage('Project logs successfully executed', $row->getDisplayId(), $output);
    }

    public function ps(string $id): Message
    {
        $row = $this->projectTable->find($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        $output = $this->worker->ps($id, $row);

        return $this->newMessage('Project ps successfully executed', $row->getDisplayId(), $output);
    }

    public function stats(string $id): Message
    {
        $row = $this->projectTable->find($id);
        if (!$row instanceof Table\Generated\ProjectRow) {
            throw new StatusCode\NotFoundException('Provided project does not exist');
        }

        $output = $this->worker->stats($id, $row);

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

    private function assertProject(Model\Project $page): void
    {
        $apps = $page->getApps();
        if ($apps === null || count($apps) === 0) {
            throw new StatusCode\BadRequestException('No apps provided');
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
