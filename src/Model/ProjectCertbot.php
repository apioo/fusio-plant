<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;

#[Description('Requests an SSL certificate for the provided domain')]
class ProjectCertbot implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $domain = null;
    protected ?string $email = null;
    public function setDomain(?string $domain): void
    {
        $this->domain = $domain;
    }
    public function getDomain(): ?string
    {
        return $this->domain;
    }
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('domain', $this->domain);
        $record->put('email', $this->email);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

