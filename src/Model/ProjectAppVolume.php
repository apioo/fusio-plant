<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;

#[Description('An app volume mounted on the server')]
class ProjectAppVolume implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $source = null;
    protected ?string $destination = null;
    public function setSource(?string $source): void
    {
        $this->source = $source;
    }
    public function getSource(): ?string
    {
        return $this->source;
    }
    public function setDestination(?string $destination): void
    {
        $this->destination = $destination;
    }
    public function getDestination(): ?string
    {
        return $this->destination;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('source', $this->source);
        $record->put('destination', $this->destination);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

