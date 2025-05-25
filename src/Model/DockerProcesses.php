<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;
/**
 * @extends Collection<DockerProcess>
 */
#[Description('A collection of all processes')]
class DockerProcesses extends Collection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
}

