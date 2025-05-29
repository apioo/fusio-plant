<?php

declare(strict_types = 1);

namespace App\Model;


class DockerLogs implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $output = null;
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
        $record->put('output', $this->output);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

