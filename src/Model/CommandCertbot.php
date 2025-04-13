<?php

declare(strict_types = 1);

namespace App\Model;


class CommandCertbot extends Command implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    /**
     * @var array<string>|null
     */
    protected ?array $domains = null;
    /**
     * @param array<string>|null $domains
     */
    public function setDomains(?array $domains): void
    {
        $this->domains = $domains;
    }
    /**
     * @return array<string>|null
     */
    public function getDomains(): ?array
    {
        return $this->domains;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = parent::toRecord();
        $record->put('domains', $this->domains);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

