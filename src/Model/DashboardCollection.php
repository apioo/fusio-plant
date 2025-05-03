<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;

#[Description('All dashboard statistics')]
class DashboardCollection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?DashboardChart $cpuPerc = null;
    protected ?DashboardChart $memPerc = null;
    protected ?DashboardChart $netioReceived = null;
    protected ?DashboardChart $netioSent = null;
    protected ?DashboardChart $blockioWritten = null;
    protected ?DashboardChart $blockioRead = null;
    public function setCpuPerc(?DashboardChart $cpuPerc): void
    {
        $this->cpuPerc = $cpuPerc;
    }
    public function getCpuPerc(): ?DashboardChart
    {
        return $this->cpuPerc;
    }
    public function setMemPerc(?DashboardChart $memPerc): void
    {
        $this->memPerc = $memPerc;
    }
    public function getMemPerc(): ?DashboardChart
    {
        return $this->memPerc;
    }
    public function setNetioReceived(?DashboardChart $netioReceived): void
    {
        $this->netioReceived = $netioReceived;
    }
    public function getNetioReceived(): ?DashboardChart
    {
        return $this->netioReceived;
    }
    public function setNetioSent(?DashboardChart $netioSent): void
    {
        $this->netioSent = $netioSent;
    }
    public function getNetioSent(): ?DashboardChart
    {
        return $this->netioSent;
    }
    public function setBlockioWritten(?DashboardChart $blockioWritten): void
    {
        $this->blockioWritten = $blockioWritten;
    }
    public function getBlockioWritten(): ?DashboardChart
    {
        return $this->blockioWritten;
    }
    public function setBlockioRead(?DashboardChart $blockioRead): void
    {
        $this->blockioRead = $blockioRead;
    }
    public function getBlockioRead(): ?DashboardChart
    {
        return $this->blockioRead;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('cpuPerc', $this->cpuPerc);
        $record->put('memPerc', $this->memPerc);
        $record->put('netioReceived', $this->netioReceived);
        $record->put('netioSent', $this->netioSent);
        $record->put('blockioWritten', $this->blockioWritten);
        $record->put('blockioRead', $this->blockioRead);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

