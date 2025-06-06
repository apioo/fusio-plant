<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;

#[Description('Contains a message whether the operation was successful or not')]
class Message implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?bool $success = null;
    protected ?string $message = null;
    protected ?string $id = null;
    protected ?string $output = null;
    public function setSuccess(?bool $success): void
    {
        $this->success = $success;
    }
    public function getSuccess(): ?bool
    {
        return $this->success;
    }
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }
    public function getMessage(): ?string
    {
        return $this->message;
    }
    public function setId(?string $id): void
    {
        $this->id = $id;
    }
    public function getId(): ?string
    {
        return $this->id;
    }
    public function setOutput(?string $output): void
    {
        $this->output = $output;
    }
    public function getOutput(): ?string
    {
        return $this->output;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('success', $this->success);
        $record->put('message', $this->message);
        $record->put('id', $this->id);
        $record->put('output', $this->output);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

