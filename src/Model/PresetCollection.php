<?php

declare(strict_types = 1);

namespace App\Model;

use PSX\Schema\Attribute\Description;
/**
 * @extends Collection<Preset>
 */
#[Description('A collection of all presets')]
class PresetCollection extends Collection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
}

