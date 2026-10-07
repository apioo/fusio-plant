<?php

declare(strict_types = 1);

namespace App\Model;


class MuninReport implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?string $html = null;
    public function setHtml(?string $html): void
    {
        $this->html = $html;
    }
    public function getHtml(): ?string
    {
        return $this->html;
    }
    /**
     * @return \PSX\Record\RecordInterface<mixed>
     */
    public function toRecord(): \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('html', $this->html);
        return $record;
    }
    public function jsonSerialize(): object
    {
        return (object) $this->toRecord()->getAll();
    }
}

