<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;
/**
 * @extends Collection<DockerImage>
 */
#[Description('A collection of all images')]
class DockerImages extends Collection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
}

