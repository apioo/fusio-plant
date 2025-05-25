<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\DerivedType;
use PSX\Schema\Attribute\Discriminator;

#[Discriminator('type')]
#[DerivedType(CommandProjectSetup::class, 'project-setup')]
#[DerivedType(CommandProjectRemove::class, 'project-remove')]
#[DerivedType(CommandProjectDown::class, 'project-down')]
#[DerivedType(CommandProjectLogs::class, 'project-logs')]
#[DerivedType(CommandProjectPs::class, 'project-ps')]
#[DerivedType(CommandProjectPull::class, 'project-pull')]
#[DerivedType(CommandProjectStats::class, 'project-stats')]
#[DerivedType(CommandProjectUp::class, 'project-up')]
#[DerivedType(CommandCertbot::class, 'certbot')]
#[DerivedType(CommandImages::class, 'images')]
#[DerivedType(CommandLogin::class, 'login')]
#[DerivedType(CommandPs::class, 'ps')]
#[DerivedType(CommandStats::class, 'stats')]
abstract class Command implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $type = null;
    public function setType(?string $type): void
    {
        $this->type = $type;
    }
    public function getType(): ?string
    {
        return $this->type;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('type', $this->type);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

