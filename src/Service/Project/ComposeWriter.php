<?php

namespace App\Service\Project;

use App\Model\ProjectApp;
use PSX\Record\Record;
use Symfony\Component\Yaml\Yaml;

class ComposeWriter
{
    public function __construct(private PortNumberResolver $portNumberResolver)
    {
    }

    /**
     * @param array<ProjectApp> $apps
     */
    public function write(int $id, array $apps): string
    {
        $services = [];
        foreach ($apps as $index => $app) {
            $services[$app->getName()] = $this->buildConfigForApp($id, $index, $app);
        }

        return Yaml::dump([
            'services' => $services,
        ]);
    }

    private function buildConfigForApp(int $id, int $index, ProjectApp $app): array
    {
        $return = [
            'image' => $app->getImage(),
            'restart' => 'always',
        ];

        $environment = $app->getEnvironment();
        if ($environment instanceof Record) {
            $return['environment'] = $environment->getAll();
        }

        $volumes = $app->getVolumes();
        if (is_array($volumes)) {
            $list = [];
            foreach ($volumes as $volume) {
                $list[] = $volume->getSource() . ':' . $volume->getDestination();
            }

            $return['volumes'] = $list;
        }

        $links = $app->getLinks();
        if (is_array($volumes)) {
            $return['links'] = $links;
        }

        $domain = $app->getDomain();
        if ($domain !== null && $domain !== '') {
            $internalPort = $this->portNumberResolver->resolve($id, $index);
            $port = $app->getPort() ?? 80;

            $return['ports'] = [
                '127.0.0.1:' . $internalPort . ':' . $port
            ];
        }

        return $return;
    }
}
