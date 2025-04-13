<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;
/**
 * @extends Collection<Project>
 */
#[Description('A collection of all projects')]
class ProjectCollection extends Collection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
}

