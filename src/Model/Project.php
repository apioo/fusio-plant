<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;

#[Description('A project is logical unit which contains multiple apps i.e. a backend and database app')]
class Project implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $id = null;
    protected ?string $name = null;
    /**
     * @var array<ProjectApp>|null
     */
    protected ?array $apps = null;
    protected ?\PSX\DateTime\LocalDateTime $updateDate = null;
    protected ?\PSX\DateTime\LocalDateTime $insertDate = null;
    public function setId(?string $id): void
    {
        $this->id = $id;
    }
    public function getId(): ?string
    {
        return $this->id;
    }
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    /**
     * @param array<ProjectApp>|null $apps
     */
    public function setApps(?array $apps): void
    {
        $this->apps = $apps;
    }
    /**
     * @return array<ProjectApp>|null
     */
    public function getApps(): ?array
    {
        return $this->apps;
    }
    public function setUpdateDate(?\PSX\DateTime\LocalDateTime $updateDate): void
    {
        $this->updateDate = $updateDate;
    }
    public function getUpdateDate(): ?\PSX\DateTime\LocalDateTime
    {
        return $this->updateDate;
    }
    public function setInsertDate(?\PSX\DateTime\LocalDateTime $insertDate): void
    {
        $this->insertDate = $insertDate;
    }
    public function getInsertDate(): ?\PSX\DateTime\LocalDateTime
    {
        return $this->insertDate;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('id', $this->id);
        $record->put('name', $this->name);
        $record->put('apps', $this->apps);
        $record->put('updateDate', $this->updateDate);
        $record->put('insertDate', $this->insertDate);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

