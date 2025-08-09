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

namespace App\Service\Project;

use App\Exception\ConfigurationException;
use App\Exception\PortResolveException;
use App\Model;

readonly class NginxWriter
{
    public function __construct(private PortNumberResolver $portNumberResolver)
    {
    }

    /**
     * @throws ConfigurationException
     * @throws PortResolveException
     */
    public function write(int $id, Model\Project $project): string
    {
        $configs = [];
        foreach ($project->getApps() as $index => $app) {
            $domains = $app->getDomains() ?? [];
            if (count($domains) === 0) {
                // no nginx config needed in case there are no domains
                continue;
            }

            $configs[] = $this->writeConfigForApp($id, $index, $app);
        }

        return implode("\n", $configs) . "\n";
    }

    /**
     * @throws ConfigurationException
     * @throws PortResolveException
     */
    private function writeConfigForApp(int $id, int $index, Model\ProjectApp $app): string
    {
        $appId = $id . '_' . $app->getName();
        $allDomains = $app->getDomains() ?? [];
        $domains = implode(' ', $allDomains);
        if (empty($domains)) {
            throw new ConfigurationException('You must provide at least one domain');
        }

        $internalPort = $this->portNumberResolver->resolve($id, $index);

        $config = [];
        if ($app->getCache()) {
            $config[] = 'proxy_cache_path /cache/' . $appId . ' keys_zone=' . $appId . ':10m;';
        }
        $config[] = 'server {';
        $config[] = '  server_name ' . $domains . ';';
        $config[] = '  location / {';
        $config[] = '    proxy_pass http://127.0.0.1:' . $internalPort . ';';
        $config[] = '    proxy_set_header Host $host;';
        $config[] = '    proxy_set_header X-Forwarded-For $remote_addr;';
        $config[] = '    proxy_set_header X-Forwarded-Proto $scheme;';
        if ($app->getCache()) {
            $config[] = '    proxy_cache ' . $appId . ';';
            $config[] = '    proxy_cache_valid 200 24h;';
        }
        $config[] = '  }';
        $config[] = '}';

        // for www domain we automatically add redirect
        $wwwDomain = reset($allDomains);
        if ($wwwDomain && str_starts_with($wwwDomain, 'www.')) {
            $nonWwwDomain = str_replace('www.', '', $wwwDomain);
            if (!in_array($nonWwwDomain, $allDomains)) {
                $config[] = 'server {';
                $config[] = '  server_name ' . $nonWwwDomain . ';';
                $config[] = '  if ($host = ' . $nonWwwDomain . ') {';
                $config[] = '    return 301 $scheme://' . $wwwDomain . '$request_uri;';
                $config[] = '  }';
                $config[] = '}';
            }
        }

        return implode("\n", $config) . "\n";
    }
}
