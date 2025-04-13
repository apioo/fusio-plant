<?php

declare(strict_types = 1);

namespace App\Model;


class CommandLogin extends Command implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $username = null;
    protected ?string $password = null;
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
        $record->put('username', $this->username);
        $record->put('password', $this->password);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

