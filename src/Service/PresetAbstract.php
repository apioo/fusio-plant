<?php
/*
 * This file is part of the Fusio Plant project (https://github.com/apioo/fusio-plant).
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

use App\Model\ProjectApp;
use App\Model\ProjectAppVolume;
use Fusio\Engine\Inflection\ClassName;
use PSX\Record\Record;
use Symfony\Component\DependencyInjection\Container;

abstract class PresetAbstract implements PresetInterface
{
    public static function getName(): string
    {
        return ClassName::serialize(static::class);
    }

    public function getDisplayName(): string
    {
        return str_replace(' ', '-', ucwords(str_replace('_', ' ', Container::underscore((new \ReflectionClass($this))->getShortName()))));
    }

    protected function newApp(string $name, string $image, ?array $domains = null, ?bool $cache = null, ?int $port = null, ?string $command = null, ?Record $environment = null, ?array $links = null, ?array $volumes = null): ProjectApp
    {
        $app = new ProjectApp();
        $app->setName($name);
        $app->setImage($image);
        $app->setDomains($domains ?? []);
        $app->setCache($cache);
        $app->setPort($port);
        $app->setCommand($command);
        $app->setEnvironment($environment ?? new Record());
        $app->setVolumes($volumes ?? []);
        $app->setLinks($links ?? []);
        return $app;
    }

    /**
     * @param string $source - The source of the mount. For named volumes, this is the name of the volume. For anonymous volumes, this field is omitted.
     * @param string $destination - The path where the file or directory is mounted in the container.
     */
    protected function newVolume(string $source, string $destination): ProjectAppVolume
    {
        $volume = new ProjectAppVolume();
        $volume->setSource($source);
        $volume->setDestination($destination);
        return $volume;
    }

    protected function addLink(?ProjectApp $app, string $link): void
    {
        if (!$app instanceof ProjectApp) {
            return;
        }

        $app->setLinks(array_merge($app->getLinks(), [$link]));
    }
}
