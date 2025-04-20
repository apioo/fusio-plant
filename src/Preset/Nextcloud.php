<?php

namespace App\Preset;

use App\Service\PresetAbstract;
use PSX\Record\Record;

class Nextcloud extends PresetAbstract
{
    public function load(): array
    {
        $mysqlNextCloudPassword = substr(sha1(random_bytes(40)), 0, 16);

        $nextcloudEnv = Record::fromArray([
            'MYSQL_DATABASE' => 'nextcloud',
            'MYSQL_USER' => 'nextcloud',
            'MYSQL_PASSWORD' => $mysqlNextCloudPassword,
            'MYSQL_HOST' => 'mysql',
        ]);

        $mysqlEnv = Record::fromArray([
            'MYSQL_DATABASE' => 'nextcloud',
            'MYSQL_USER' => 'nextcloud',
            'MYSQL_PASSWORD' => $mysqlNextCloudPassword,
            'MYSQL_RANDOM_ROOT_PASSWORD' => '1',
        ]);

        return [
            $this->newApp('nextcloud', 'nextcloud:31-apache', environment: $nextcloudEnv, links: ['mysql'], volumes: [
                $this->newVolume('./data', '/var/www/html'),
            ]),
            $this->newApp('mysql', 'mysql:8.0', environment: $mysqlEnv, volumes: [
                $this->newVolume('./db', '/var/lib/mysql')
            ]),
        ];
    }
}
