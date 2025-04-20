<?php

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
