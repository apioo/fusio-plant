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

        $message = new Message();
        $message->setSuccess(true);
        $message->setMessage('Project successful created');
        $message->setId($row->getDisplayId());
        $message->setOutput($output);
        return $message;
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

        $message = new Message();
        $message->setSuccess(true);
        $message->setMessage('Project successful updated');
        $message->setId($row->getDisplayId());
        $message->setOutput($output);
        return $message;
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

        $message = new Message();
        $message->setSuccess(true);
        $message->setMessage('Project successful deleted');
        $message->setId($row->getDisplayId());
        $message->setOutput($output);
        return $message;
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
}
