<?php

namespace App\Table\Generated;

class MonitorRow implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    private ?int $id = null;
    private ?int $projectId = null;
    private ?string $containerId = null;
    private ?string $name = null;
    private ?string $cpuPerc = null;
    private ?string $memPerc = null;
    private ?int $memUsage = null;
    private ?int $memLimit = null;
    private ?int $netioReceived = null;
    private ?int $netioSent = null;
    private ?int $blockioWritten = null;
    private ?int $blockioRead = null;
    private ?\PSX\DateTime\LocalDateTime $insertDate = null;
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function getId(): int
    {
        return $this->id ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "id" was provided');
    }
    public function setProjectId(int $projectId): void
    {
        $this->projectId = $projectId;
    }
    public function getProjectId(): int
    {
        return $this->projectId ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "project_id" was provided');
    }
    public function setContainerId(string $containerId): void
    {
        $this->containerId = $containerId;
    }
    public function getContainerId(): string
    {
        return $this->containerId ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "container_id" was provided');
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    public function getName(): string
    {
        return $this->name ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "name" was provided');
    }
    public function setCpuPerc(string $cpuPerc): void
    {
        $this->cpuPerc = $cpuPerc;
    }
    public function getCpuPerc(): string
    {
        return $this->cpuPerc ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "cpu_perc" was provided');
    }
    public function setMemPerc(string $memPerc): void
    {
        $this->memPerc = $memPerc;
    }
    public function getMemPerc(): string
    {
        return $this->memPerc ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "mem_perc" was provided');
    }
    public function setMemUsage(int $memUsage): void
    {
        $this->memUsage = $memUsage;
    }
    public function getMemUsage(): int
    {
        return $this->memUsage ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "mem_usage" was provided');
    }
    public function setMemLimit(int $memLimit): void
    {
        $this->memLimit = $memLimit;
    }
    public function getMemLimit(): int
    {
        return $this->memLimit ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "mem_limit" was provided');
    }
    public function setNetioReceived(int $netioReceived): void
    {
        $this->netioReceived = $netioReceived;
    }
    public function getNetioReceived(): int
    {
        return $this->netioReceived ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "netio_received" was provided');
    }
    public function setNetioSent(int $netioSent): void
    {
        $this->netioSent = $netioSent;
    }
    public function getNetioSent(): int
    {
        return $this->netioSent ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "netio_sent" was provided');
    }
    public function setBlockioWritten(int $blockioWritten): void
    {
        $this->blockioWritten = $blockioWritten;
    }
    public function getBlockioWritten(): int
    {
        return $this->blockioWritten ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "blockio_written" was provided');
    }
    public function setBlockioRead(int $blockioRead): void
    {
        $this->blockioRead = $blockioRead;
    }
    public function getBlockioRead(): int
    {
        return $this->blockioRead ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "blockio_read" was provided');
    }
    public function setInsertDate(\PSX\DateTime\LocalDateTime $insertDate): void
    {
        $this->insertDate = $insertDate;
    }
    public function getInsertDate(): \PSX\DateTime\LocalDateTime
    {
        return $this->insertDate ?? throw new \PSX\Sql\Exception\NoValueAvailable('No value for required column "insert_date" was provided');
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('id', $this->id);
        $record->put('project_id', $this->projectId);
        $record->put('container_id', $this->containerId);
        $record->put('name', $this->name);
        $record->put('cpu_perc', $this->cpuPerc);
        $record->put('mem_perc', $this->memPerc);
        $record->put('mem_usage', $this->memUsage);
        $record->put('mem_limit', $this->memLimit);
        $record->put('netio_received', $this->netioReceived);
        $record->put('netio_sent', $this->netioSent);
        $record->put('blockio_written', $this->blockioWritten);
        $record->put('blockio_read', $this->blockioRead);
        $record->put('insert_date', $this->insertDate);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
    public static function from(array|\ArrayAccess $data): self
    {
        $row = new self();
        $row->id = isset($data['id']) && is_int($data['id']) ? $data['id'] : null;
        $row->projectId = isset($data['project_id']) && is_int($data['project_id']) ? $data['project_id'] : null;
        $row->containerId = isset($data['container_id']) && is_string($data['container_id']) ? $data['container_id'] : null;
        $row->name = isset($data['name']) && is_string($data['name']) ? $data['name'] : null;
        $row->cpuPerc = isset($data['cpu_perc']) && is_string($data['cpu_perc']) ? $data['cpu_perc'] : null;
        $row->memPerc = isset($data['mem_perc']) && is_string($data['mem_perc']) ? $data['mem_perc'] : null;
        $row->memUsage = isset($data['mem_usage']) && is_int($data['mem_usage']) ? $data['mem_usage'] : null;
        $row->memLimit = isset($data['mem_limit']) && is_int($data['mem_limit']) ? $data['mem_limit'] : null;
        $row->netioReceived = isset($data['netio_received']) && is_int($data['netio_received']) ? $data['netio_received'] : null;
        $row->netioSent = isset($data['netio_sent']) && is_int($data['netio_sent']) ? $data['netio_sent'] : null;
        $row->blockioWritten = isset($data['blockio_written']) && is_int($data['blockio_written']) ? $data['blockio_written'] : null;
        $row->blockioRead = isset($data['blockio_read']) && is_int($data['blockio_read']) ? $data['blockio_read'] : null;
        $row->insertDate = isset($data['insert_date']) && $data['insert_date'] instanceof \DateTimeInterface ? \PSX\DateTime\LocalDateTime::from($data['insert_date']) : null;
        return $row;
    }
}