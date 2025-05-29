<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;
use PSX\Schema\Attribute\Key;

class DockerProcess implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    #[Key('ID')]
    #[Description('')]
    protected ?string $iD = null;
    #[Key('Command')]
    #[Description('')]
    protected ?string $command = null;
    #[Key('Image')]
    #[Description('')]
    protected ?string $image = null;
    #[Key('Labels')]
    #[Description('')]
    protected ?string $labels = null;
    #[Key('LocalVolumes')]
    #[Description('')]
    protected ?string $localVolumes = null;
    #[Key('Mounts')]
    #[Description('')]
    protected ?string $mounts = null;
    #[Key('Names')]
    #[Description('')]
    protected ?string $names = null;
    #[Key('Networks')]
    #[Description('')]
    protected ?string $networks = null;
    #[Key('Ports')]
    #[Description('')]
    protected ?string $ports = null;
    #[Key('RunningFor')]
    #[Description('')]
    protected ?string $runningFor = null;
    #[Key('Size')]
    #[Description('')]
    protected ?string $size = null;
    #[Key('State')]
    #[Description('')]
    protected ?string $state = null;
    #[Key('Status')]
    #[Description('')]
    protected ?string $status = null;
    #[Key('CreatedAt')]
    #[Description('')]
    protected ?string $createdAt = null;
    public function setID(?string $iD): void
    {
        $this->iD = $iD;
    }
    public function getID(): ?string
    {
        return $this->iD;
    }
    public function setCommand(?string $command): void
    {
        $this->command = $command;
    }
    public function getCommand(): ?string
    {
        return $this->command;
    }
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }
    public function getImage(): ?string
    {
        return $this->image;
    }
    public function setLabels(?string $labels): void
    {
        $this->labels = $labels;
    }
    public function getLabels(): ?string
    {
        return $this->labels;
    }
    public function setLocalVolumes(?string $localVolumes): void
    {
        $this->localVolumes = $localVolumes;
    }
    public function getLocalVolumes(): ?string
    {
        return $this->localVolumes;
    }
    public function setMounts(?string $mounts): void
    {
        $this->mounts = $mounts;
    }
    public function getMounts(): ?string
    {
        return $this->mounts;
    }
    public function setNames(?string $names): void
    {
        $this->names = $names;
    }
    public function getNames(): ?string
    {
        return $this->names;
    }
    public function setNetworks(?string $networks): void
    {
        $this->networks = $networks;
    }
    public function getNetworks(): ?string
    {
        return $this->networks;
    }
    public function setPorts(?string $ports): void
    {
        $this->ports = $ports;
    }
    public function getPorts(): ?string
    {
        return $this->ports;
    }
    public function setRunningFor(?string $runningFor): void
    {
        $this->runningFor = $runningFor;
    }
    public function getRunningFor(): ?string
    {
        return $this->runningFor;
    }
    public function setSize(?string $size): void
    {
        $this->size = $size;
    }
    public function getSize(): ?string
    {
        return $this->size;
    }
    public function setState(?string $state): void
    {
        $this->state = $state;
    }
    public function getState(): ?string
    {
        return $this->state;
    }
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }
    public function getStatus(): ?string
    {
        return $this->status;
    }
    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('ID', $this->iD);
        $record->put('Command', $this->command);
        $record->put('Image', $this->image);
        $record->put('Labels', $this->labels);
        $record->put('LocalVolumes', $this->localVolumes);
        $record->put('Mounts', $this->mounts);
        $record->put('Names', $this->names);
        $record->put('Networks', $this->networks);
        $record->put('Ports', $this->ports);
        $record->put('RunningFor', $this->runningFor);
        $record->put('Size', $this->size);
        $record->put('State', $this->state);
        $record->put('Status', $this->status);
        $record->put('CreatedAt', $this->createdAt);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

