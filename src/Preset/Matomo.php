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

class Matomo extends PresetAbstract
{
    public function load(): array
    {
        $mysqlMatomoPassword = substr(sha1(random_bytes(40)), 0, 16);

        $matomoEnv = Record::fromArray([
            'MATOMO_DATABASE_HOST' => 'mysql',
            'MATOMO_DATABASE_USERNAME' => 'matomo',
            'MATOMO_DATABASE_PASSWORD' => $mysqlMatomoPassword,
            'MATOMO_DATABASE_DBNAME' => 'matomo',
        ]);

        $mysqlEnv = Record::fromArray([
            'MYSQL_DATABASE' => 'matomo',
            'MYSQL_USER' => 'matomo',
            'MYSQL_PASSWORD' => $mysqlMatomoPassword,
            'MYSQL_RANDOM_ROOT_PASSWORD' => '1',
        ]);

        return [
            $this->newApp('matomo', 'matomo:5-apache', environment: $matomoEnv, links: ['mysql'], volumes: [
                $this->newVolume('./data', '/var/www/html'),
            ]),
            $this->newApp('mysql', 'mysql:8.0', environment: $mysqlEnv, volumes: [
                $this->newVolume('./db', '/var/lib/mysql')
            ]),
        ];
    }
}
