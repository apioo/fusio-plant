<?php

declare(strict_types = 1);

namespace App\Model;


class CommandProjectSetup extends Command implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $name = null;
    protected ?string $compose = null;
    protected ?string $nginx = null;
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setCompose(?string $compose): void
    {
        $this->compose = $compose;
    }
    public function getCompose(): ?string
    {
        return $this->compose;
    }
    public function setNginx(?string $nginx): void
    {
        $this->nginx = $nginx;
    }
    public function getNginx(): ?string
    {
        return $this->nginx;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = parent::toRecord();
        $record->put('name', $this->name);
        $record->put('compose', $this->compose);
        $record->put('nginx', $this->nginx);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

