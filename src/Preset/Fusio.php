<?php

namespace App\Preset;

use App\Service\PresetAbstract;
use PSX\Record\Record;

class Fusio extends PresetAbstract
{
    public function load(): array
    {
        $projectKey = sha1(random_bytes(40));
        $mysqlFusioPassword = substr(sha1(random_bytes(40)), 0, 16);

        $backendUser = 'fusio';
        $backendEmail = 'info@mydomain.com';
        $backendPassword = substr(sha1(random_bytes(40)), 0, 16);

        $fusioEnv = Record::fromArray([
            'FUSIO_TENANT_ID' => '',
            'FUSIO_PROJECT_KEY' => $projectKey,
            'FUSIO_URL' => '',
            'FUSIO_APPS_URL' => '',
            'FUSIO_ENV' => 'prod',
            'FUSIO_DEBUG' => 'false',
            'FUSIO_CONNECTION' => 'pdo-mysql://fusio:' . $mysqlFusioPassword . '@mysql/fusio',
            'FUSIO_BACKEND_USER' => $backendUser,
            'FUSIO_BACKEND_EMAIL' => $backendEmail,
            'FUSIO_BACKEND_PW' => $backendPassword,
            'FUSIO_MAILER' => '',
            'FUSIO_MAIL_SENDER' => '',
            'FUSIO_TRUSTED_IP_HEADER' => 'X-Forwarded-For',
            'STRIPE_API_KEY' => '',
            'STRIPE_WEBHOOK_KEY' => '',
            'RECAPTCHA_KEY' => '',
            'RECAPTCHA_SECRET' => '',
        ]);

        $mysqlEnv = Record::fromArray([
            'MYSQL_USER' => 'fusio',
            'MYSQL_PASSWORD' => $mysqlFusioPassword,
            'MYSQL_DATABASE' => 'fusio',
            'MYSQL_RANDOM_ROOT_PASSWORD' => '1',
        ]);

        return [
            $this->newApp('fusio', 'fusio/fusio:5.2', environment: $fusioEnv, links: ['mysql']),
            $this->newApp('mysql', 'mysql:8.0', environment: $mysqlEnv, volumes: [
                $this->newVolume('./db', '/var/lib/mysql')
            ]),
        ];
    }
}
