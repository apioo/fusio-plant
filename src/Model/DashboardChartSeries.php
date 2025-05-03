<?php

declare(strict_types = 1);

namespace App\Model;


class DashboardChartSeries implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $name = null;
    /**
     * @var array<float>|null
     */
    protected ?array $data = null;
    public function setName(?string $name): void
    {
        $this->name = $name;
    }
    public function getName(): ?string
    {
        return $this->name;
    }
    /**
     * @param array<float>|null $data
     */
    public function setData(?array $data): void
    {
        $this->data = $data;
    }
    /**
     * @return array<float>|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('name', $this->name);
        $record->put('data', $this->data);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

