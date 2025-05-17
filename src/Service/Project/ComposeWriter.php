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

namespace App\Service\Project;

use App\Model\ProjectApp;
use PSX\Record\Record;
use Symfony\Component\Yaml\Yaml;

readonly class ComposeWriter
{
    public function __construct(private PortNumberResolver $portNumberResolver)
    {
    }

    /**
     * @param array<ProjectApp> $apps
     */
    public function write(int $id, array $apps): string
    {
        $services = [];
        foreach ($apps as $index => $app) {
            $services[$app->getName()] = $this->buildConfigForApp($id, $index, $app);
        }

        return Yaml::dump([
            'services' => $services,
        ], inline: 12);
    }

    private function buildConfigForApp(int $id, int $index, ProjectApp $app): array
    {
        $return = [
            'image' => $app->getImage(),
            'restart' => 'always',
            'labels' => [
                'org.fusio-project.plant.project' => $id,
            ],
        ];

        $environment = $app->getEnvironment();
        if ($environment instanceof Record) {
            $return['environment'] = $environment->getAll();
        }

        $volumes = $app->getVolumes();
        if (is_array($volumes) && count($volumes) > 0) {
            $list = [];
            foreach ($volumes as $volume) {
                $list[] = $volume->getSource() . ':' . $volume->getDestination();
            }

            $return['volumes'] = $list;
        }

        $links = $app->getLinks();
        if (is_array($links) && count($links) > 0) {
            $return['links'] = $links;
        }

        $domains = $app->getDomains() ?? [];
        if (count($domains) > 0) {
            $internalPort = $this->portNumberResolver->resolve($id, $index);
            $port = $app->getPort() ?? 80;

            $return['ports'] = [
                '127.0.0.1:' . $internalPort . ':' . $port
            ];
        }

        return $return;
    }
}
