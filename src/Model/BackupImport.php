<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;

#[Description('Imports a project configuration backup')]
class BackupImport implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $import = null;
    public function setImport(?string $import): void
    {
        $this->import = $import;
    }
    public function getImport(): ?string
    {
        return $this->import;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('import', $this->import);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

