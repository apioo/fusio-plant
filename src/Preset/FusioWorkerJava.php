<?php

namespace App\Preset;

class FusioWorkerJava extends Fusio
{
    public function load(): array
    {
        $apps = parent::load();

        $this->addLink($apps[0] ?? null, 'worker-java');

        $apps[] = $this->newApp('worker-java', 'fusio/worker-java:2.0', volumes: [
            $this->newVolume('./worker/java', '/worker/actions')
        ]);

        return $apps;
    }
}
