<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;

#[Description('A collection of all projects')]
class ProjectCollection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
}

