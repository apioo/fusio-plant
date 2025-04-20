<?php

namespace App\Preset;

class FusioWorkerPython extends Fusio
{
    public function load(): array
    {
        $apps = parent::load();

        $this->addLink($apps[0] ?? null, 'worker-python');

        $apps[] = $this->newApp('worker-python', 'fusio/worker-python:2.0', volumes: [
            $this->newVolume('./worker/python', '/worker/actions')
        ]);

        return $apps;
    }
}
