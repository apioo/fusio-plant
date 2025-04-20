<?php

namespace App\Preset;

use App\Service\PresetAbstract;
use PSX\Record\Record;

class Wordpress extends PresetAbstract
{
    public function load(): array
    {
        $mysqlWordpressPassword = substr(sha1(random_bytes(40)), 0, 16);

        $wordpressEnv = Record::fromArray([
            'WORDPRESS_DB_HOST' => 'mysql',
            'WORDPRESS_DB_USER' => 'wordpress',
            'WORDPRESS_DB_PASSWORD' => $mysqlWordpressPassword,
            'WORDPRESS_DB_NAME' => 'mysql',
        ]);

        $mysqlEnv = Record::fromArray([
            'MYSQL_DATABASE' => 'wordpress',
            'MYSQL_USER' => 'wordpress',
            'MYSQL_PASSWORD' => $mysqlWordpressPassword,
            'MYSQL_RANDOM_ROOT_PASSWORD' => '1',
        ]);

        return [
            $this->newApp('wordpress', 'wordpress:6-apache', environment: $wordpressEnv, links: ['mysql'], volumes: [
                $this->newVolume('./data', '/var/www/html'),
            ]),
            $this->newApp('mysql', 'mysql:8.0', environment: $mysqlEnv, volumes: [
                $this->newVolume('./db', '/var/lib/mysql')
            ]),
        ];
    }
}
