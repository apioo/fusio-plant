<?php

namespace App\Preset;

class FusioWorkerJavascript extends Fusio
{
    public function load(): array
    {
        $apps = parent::load();

        $this->addLink($apps[0] ?? null, 'worker-javascript');

        $apps[] = $this->newApp('worker-javascript', 'fusio/worker-javascript:2.0', volumes: [
            $this->newVolume('./worker/javascript', '/worker/actions')
        ]);

        return $apps;
    }
}
