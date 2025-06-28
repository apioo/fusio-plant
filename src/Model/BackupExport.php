<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;

#[Description('Exports a project configuration backup')]
class BackupExport implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $export = null;
    public function setExport(?string $export): void
    {
        $this->export = $export;
    }
    public function getExport(): ?string
    {
        return $this->export;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('export', $this->export);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

