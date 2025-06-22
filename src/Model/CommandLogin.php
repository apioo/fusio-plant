<?php

declare(strict_types = 1);

namespace App\Model;


class CommandLogin extends Command implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $domain = null;
    protected ?string $username = null;
    protected ?string $password = null;
    public function setDomain(?string $domain): void
    {
        $this->domain = $domain;
    }
    public function getDomain(): ?string
    {
        return $this->domain;
    }
    public function setUsername(?string $username): void
    {
        $this->username = $username;
    }
    public function getUsername(): ?string
    {
        return $this->username;
    }
    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }
    public function getPassword(): ?string
    {
        return $this->password;
    }
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = parent::toRecord();
        $record->put('domain', $this->domain);
        $record->put('username', $this->username);
        $record->put('password', $this->password);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

