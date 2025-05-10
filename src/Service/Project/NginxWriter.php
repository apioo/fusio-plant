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

use App\Exception\ConfigurationException;
use App\Exception\PortResolveException;
use App\Model\ProjectApp;

readonly class NginxWriter
{
    public function __construct(private PortNumberResolver $portNumberResolver)
    {
    }

    /**
     * @param array<ProjectApp> $apps
     * @throws ConfigurationException
     * @throws PortResolveException
     */
    public function write(int $id, array $apps): string
    {
        $configs = [];
        foreach ($apps as $index => $app) {
            $configs[] = $this->writeConfigForApp($id, $index, $app);
        }

        return implode("\n", $configs) . "\n";
    }

    /**
     * @throws ConfigurationException
     * @throws PortResolveException
     */
    private function writeConfigForApp(int $id, int $index, ProjectApp $app): string
    {
        $appId = $id . '_' . $app->getName();
        $domains = implode(' ', $app->getDomains() ?? throw new ConfigurationException('You must provide at least one domain'));
        $internalPort = $this->portNumberResolver->resolve($id, $index);

        $config = [];
        if ($app->getCache()) {
            $config[] = 'proxy_cache_path /cache/' . $appId . ' keys_zone=' . $appId . ':10m;';
        }
        $config[] = 'server {';
        $config[] = '  server_name ' . $domains . ';';
        $config[] = '  location / {';
        $config[] = '    proxy_pass http://127.0.0.1:' . $internalPort . ';';
        $config[] = '    proxy_set_header X-Forwarded-For $remote_addr;';
        if ($app->getCache()) {
            $config[] = '    proxy_cache ' . $appId . ';';
            $config[] = '    proxy_cache_valid 200 24h;';
        }
        $config[] = '  }';
        $config[] = '}';

        return implode("\n", $config) . "\n";
    }
}
