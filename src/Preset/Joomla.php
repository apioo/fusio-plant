<?php

namespace App\Preset;

use App\Service\PresetAbstract;
use PSX\Record\Record;

class Joomla extends PresetAbstract
{
    public function load(): array
    {
        $mysqlJoomlaPassword = substr(sha1(random_bytes(40)), 0, 16);

        $joomlaEnv = Record::fromArray([
            'JOOMLA_DB_HOST' => 'db',
            'JOOMLA_DB_USER' => 'joomla',
            'JOOMLA_DB_PASSWORD' => $mysqlJoomlaPassword,
            'JOOMLA_DB_NAME' => 'joomla_db',
            'JOOMLA_SITE_NAME' => 'Joomla',
            'JOOMLA_ADMIN_USER' => 'Joomla Hero',
            'JOOMLA_ADMIN_USERNAME' => 'joomla',
            'JOOMLA_ADMIN_PASSWORD' => 'joomla@secured',
            'JOOMLA_ADMIN_EMAIL' => 'joomla@example.com',
        ]);

        $mysqlEnv = Record::fromArray([
            'MYSQL_DATABASE' => 'joomla_db',
            'MYSQL_USER' => 'joomla',
            'MYSQL_PASSWORD' => $mysqlJoomlaPassword,
            'MYSQL_RANDOM_ROOT_PASSWORD' => '1',
        ]);

        return [
            $this->newApp('joomla', 'joomla:5-apache', environment: $joomlaEnv, links: ['mysql'], volumes: [
                $this->newVolume('./data', '/var/www/html'),
            ]),
            $this->newApp('mysql', 'mysql:8.0', environment: $mysqlEnv, volumes: [
                $this->newVolume('./db', '/var/lib/mysql')
            ]),
        ];
    }
}
