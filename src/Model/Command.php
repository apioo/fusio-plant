<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\DerivedType;
use PSX\Schema\Attribute\Discriminator;

#[Discriminator('type')]
#[DerivedType(CommandSetup::class, 'setup')]
#[DerivedType(CommandRemove::class, 'remove')]
#[DerivedType(CommandCertbot::class, 'certbot')]
#[DerivedType(CommandPull::class, 'pull')]
#[DerivedType(CommandUp::class, 'up')]
#[DerivedType(CommandDown::class, 'down')]
#[DerivedType(CommandLogs::class, 'logs')]
#[DerivedType(CommandPs::class, 'ps')]
#[DerivedType(CommandStats::class, 'stats')]
#[DerivedType(CommandLogin::class, 'login')]
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

