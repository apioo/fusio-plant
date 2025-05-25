<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;
use PSX\Schema\Attribute\Key;

class DockerStatistic implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    #[Key('ID')]
    #[Description('')]
    protected ?string $iD = null;
    #[Key('Container')]
    #[Description('')]
    protected ?string $container = null;
    #[Key('Name')]
    #[Description('')]
    protected ?string $name = null;
    #[Key('PIDs')]
    #[Description('')]
    protected ?string $pIDs = null;
    #[Key('CPUPerc')]
    #[Description('')]
    protected ?string $cPUPerc = null;
    #[Key('MemPerc')]
    #[Description('')]
    protected ?string $memPerc = null;
    #[Key('MemUsage')]
    #[Description('')]
    protected ?string $memUsage = null;
    #[Key('BlockIO')]
    #[Description('')]
    protected ?string $blockIO = null;
    #[Key('NetIO')]
    #[Description('')]
    protected ?string $netIO = null;
    public function setID(?string $iD): void
    {
        $this->iD = $iD;
    }
    public function getID(): ?string
    {
        return $this->iD;
    }
    public function setContainer(?string $container): void
    {
        $this->container = $container;
    }
    public function getContainer(): ?string
    {
        return $this->container;
    }
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    public function setPIDs(?string $pIDs): void
    {
        $this->pIDs = $pIDs;
    }
    public function getPIDs(): ?string
    {
        return $this->pIDs;
    }
    public function setCPUPerc(?string $cPUPerc): void
    {
        $this->cPUPerc = $cPUPerc;
    }
    public function getCPUPerc(): ?string
    {
        return $this->cPUPerc;
    }
    public function setMemPerc(?string $memPerc): void
    {
        $this->memPerc = $memPerc;
    }
    public function getMemPerc(): ?string
    {
        return $this->memPerc;
    }
    public function setMemUsage(?string $memUsage): void
    {
        $this->memUsage = $memUsage;
    }
    public function getMemUsage(): ?string
    {
        return $this->memUsage;
    }
    public function setBlockIO(?string $blockIO): void
    {
        $this->blockIO = $blockIO;
    }
    public function getBlockIO(): ?string
    {
        return $this->blockIO;
    }
    public function setNetIO(?string $netIO): void
    {
        $this->netIO = $netIO;
    }
    public function getNetIO(): ?string
    {
        return $this->netIO;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('ID', $this->iD);
        $record->put('Container', $this->container);
        $record->put('Name', $this->name);
        $record->put('PIDs', $this->pIDs);
        $record->put('CPUPerc', $this->cPUPerc);
        $record->put('MemPerc', $this->memPerc);
        $record->put('MemUsage', $this->memUsage);
        $record->put('BlockIO', $this->blockIO);
        $record->put('NetIO', $this->netIO);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

