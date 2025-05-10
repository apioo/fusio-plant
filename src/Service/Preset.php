<?php
/*
 * This file is part of the Fusio Plant project (https://fusio-project.org/product/plant).
 * Fusio Plant is a server control panel to easily self-host apps on your server.
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Service;

use App\Model;
use Fusio\Engine\ContextInterface;
use PSX\Http\Exception\NotFoundException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class Preset
{
    /**
     * @var iterable<PresetInterface>
     */
    private iterable $presets;

    public function __construct(#[AutowireIterator('fusio.plant.preset', defaultIndexMethod: 'getName')] iterable $presets)
    {
        $this->presets = $presets;
    }

    /**
     * @return iterable<PresetInterface>
     */
    public function getAll(): iterable
    {
        return $this->presets;
    }

    public function load(string $name, ContextInterface $context): Model\Project
    {
        $apps = $this->getByName($name)->load();

        $project = new Model\Project();
        $project->setApps($apps);
        return $project;
    }

    private function getByName(string $name): PresetInterface
    {
        foreach ($this->presets as $key => $preset) {
            if ($key === $name) {
                return $preset;
            }
        }

        throw new NotFoundException('Provided preset does not exist');
    }
}
