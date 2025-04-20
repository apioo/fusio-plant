<?php

namespace App\Preset;

class FusioWorkerPHP extends Fusio
{
    public function load(): array
    {
        $apps = parent::load();

        $this->addLink($apps[0] ?? null, 'worker-php');

        $apps[] = $this->newApp('worker-php', 'fusio/worker-php:2.0', volumes: [
            $this->newVolume('./worker/php', '/worker/actions')
        ]);

        return $apps;
    }
}
