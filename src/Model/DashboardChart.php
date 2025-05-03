<?php

declare(strict_types = 1);

namespace App\Model;


class DashboardChart implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    /**
     * @var array<string>|null
     */
    protected ?array $labels = null;
    /**
     * @var array<DashboardChartSeries>|null
     */
    protected ?array $series = null;
    /**
     * @param array<string>|null $labels
     */
    public function setLabels(?array $labels): void
    {
        $this->labels = $labels;
    }
    /**
     * @return array<string>|null
     */
    public function getLabels(): ?array
    {
        return $this->labels;
    }
    /**
     * @param array<DashboardChartSeries>|null $series
     */
    public function setSeries(?array $series): void
    {
        $this->series = $series;
    }
    /**
     * @return array<DashboardChartSeries>|null
     */
    public function getSeries(): ?array
    {
        return $this->series;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('labels', $this->labels);
        $record->put('series', $this->series);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

