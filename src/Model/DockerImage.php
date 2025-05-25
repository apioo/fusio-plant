<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Key;

class DockerImage implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    #[Key('ID')]
    protected ?string $iD = null;
    #[Key('Containers')]
    protected ?string $containers = null;
    #[Key('Digest')]
    protected ?string $digest = null;
    #[Key('Repository')]
    protected ?string $repository = null;
    #[Key('SharedSize')]
    protected ?string $sharedSize = null;
    #[Key('Size')]
    protected ?string $size = null;
    #[Key('Tag')]
    protected ?string $tag = null;
    #[Key('UniqueSize')]
    protected ?string $uniqueSize = null;
    #[Key('VirtualSize')]
    protected ?string $virtualSize = null;
    #[Key('CreatedAt')]
    protected ?\PSX\DateTime\LocalDateTime $createdAt = null;
    #[Key('CreatedSince')]
    protected ?\PSX\DateTime\LocalDateTime $createdSince = null;
    public function setID(?string $iD): void
    {
        $this->iD = $iD;
    }
    public function getID(): ?string
    {
        return $this->iD;
    }
    public function setContainers(?string $containers): void
    {
        $this->containers = $containers;
    }
    public function getContainers(): ?string
    {
        return $this->containers;
    }
    public function setDigest(?string $digest): void
    {
        $this->digest = $digest;
    }
    public function getDigest(): ?string
    {
        return $this->digest;
    }
    public function setRepository(?string $repository): void
    {
        $this->repository = $repository;
    }
    public function getRepository(): ?string
    {
        return $this->repository;
    }
    public function setSharedSize(?string $sharedSize): void
    {
        $this->sharedSize = $sharedSize;
    }
    public function getSharedSize(): ?string
    {
        return $this->sharedSize;
    }
    public function setSize(?string $size): void
    {
        $this->size = $size;
    }
    public function getSize(): ?string
    {
        return $this->size;
    }
    public function setTag(?string $tag): void
    {
        $this->tag = $tag;
    }
    public function getTag(): ?string
    {
        return $this->tag;
    }
    public function setUniqueSize(?string $uniqueSize): void
    {
        $this->uniqueSize = $uniqueSize;
    }
    public function getUniqueSize(): ?string
    {
        return $this->uniqueSize;
    }
    public function setVirtualSize(?string $virtualSize): void
    {
        $this->virtualSize = $virtualSize;
    }
    public function getVirtualSize(): ?string
    {
        return $this->virtualSize;
    }
    public function setCreatedAt(?\PSX\DateTime\LocalDateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function getCreatedAt(): ?\PSX\DateTime\LocalDateTime
    {
        return $this->createdAt;
    }
    public function setCreatedSince(?\PSX\DateTime\LocalDateTime $createdSince): void
    {
        $this->createdSince = $createdSince;
    }
    public function getCreatedSince(): ?\PSX\DateTime\LocalDateTime
    {
        return $this->createdSince;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('ID', $this->iD);
        $record->put('Containers', $this->containers);
        $record->put('Digest', $this->digest);
        $record->put('Repository', $this->repository);
        $record->put('SharedSize', $this->sharedSize);
        $record->put('Size', $this->size);
        $record->put('Tag', $this->tag);
        $record->put('UniqueSize', $this->uniqueSize);
        $record->put('VirtualSize', $this->virtualSize);
        $record->put('CreatedAt', $this->createdAt);
        $record->put('CreatedSince', $this->createdSince);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

