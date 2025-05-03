<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;

#[Description('A preset config')]
class Preset implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $name = null;
    protected ?string $displayName = null;
    /**
     * @var array<ProjectApp>|null
     */
    protected ?array $apps = null;
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }
    public function getDisplayName(): ?string
    {
        return $this->displayName;
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
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('name', $this->name);
        $record->put('displayName', $this->displayName);
        $record->put('apps', $this->apps);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

