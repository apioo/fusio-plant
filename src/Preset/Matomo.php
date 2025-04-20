<?php

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
