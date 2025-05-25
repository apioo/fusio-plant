<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;
/**
 * @extends Collection<DockerStatistic>
 */
#[Description('A collection of all statistics')]
class DockerStatistics extends Collection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
}

