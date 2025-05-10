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

namespace App\Preset;

use App\Service\PresetAbstract;
use PSX\Record\Record;

class Drupal extends PresetAbstract
{
    public function load(): array
    {
        $postgresPassword = substr(sha1(random_bytes(40)), 0, 16);

        $postgresEnv = Record::fromArray([
            'POSTGRES_PASSWORD' => $postgresPassword,
        ]);

        return [
            $this->newApp('drupal', 'drupal:10-apache', links: ['postgres'], volumes: [
                $this->newVolume('./modules', '/var/www/html/modules'),
                $this->newVolume('./profiles', '/var/www/html/profiles'),
                $this->newVolume('./themes', '/var/www/html/themes'),
                $this->newVolume('./sites', '/var/www/html/sites'),
            ]),
            $this->newApp('postgres', 'postgres:16', environment: $postgresEnv, volumes: [
                $this->newVolume('./db', '/var/lib/postgresql/data')
            ]),
        ];
    }
}
