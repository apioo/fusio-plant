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

class Keycloak extends PresetAbstract
{
    public function load(): array
    {
        $mysqlKeycloakPassword = substr(sha1(random_bytes(40)), 0, 16);
        $adminPassword = substr(sha1(random_bytes(40)), 0, 16);

        $keycloakEnv = Record::fromArray([
            'KEYCLOAK_ADMIN' => 'admin',
            'KEYCLOAK_ADMIN_PASSWORD' => $adminPassword,
            'KC_HOSTNAME' => 'myhost.com',
            'KC_DB' => 'mysql',
            'KC_DB_URL' => 'jdbc:mysql://mysql/keycloak',
            'KC_DB_USERNAME' => 'keycloak',
            'KC_DB_PASSWORD' => $mysqlKeycloakPassword,
            'KC_PROXY_HEADERS' => 'X-Forwarded-For',
            'KC_HTTP_ENABLED' => 'true',
            'PROXY_ADDRESS_FORWARDING' => 'true',
        ]);

        $mysqlEnv = Record::fromArray([
            'MYSQL_DATABASE' => 'keycloak',
            'MYSQL_USER' => 'keycloak',
            'MYSQL_PASSWORD' => $mysqlKeycloakPassword,
            'MYSQL_RANDOM_ROOT_PASSWORD' => '1',
        ]);

        return [
            $this->newApp('keycloak', 'quay.io/keycloak/keycloak:26.2.5', port: 8080, command: 'start', environment: $keycloakEnv, links: ['mysql']),
            $this->newApp('mysql', 'mysql:8.4', environment: $mysqlEnv, volumes: [
                $this->newVolume('./db', '/var/lib/mysql')
            ]),
        ];
    }
}
