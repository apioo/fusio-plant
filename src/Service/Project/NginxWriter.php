<?php

namespace App\Service\Project;

use App\Exception\ConfigurationException;
use App\Model\ProjectApp;

readonly class NginxWriter
{
    public function __construct(private PortNumberResolver $portNumberResolver)
    {
    }

    /**
     * @param array<ProjectApp> $apps
     */
    public function write(int $id, array $apps): string
    {
        $configs = [];
        foreach ($apps as $index => $app) {
            $configs[] = $this->writeConfigForApp($id, $index, $app);
        }

        return implode("\n", $configs) . "\n";
    }

    private function writeConfigForApp(int $id, int $index, ProjectApp $app): string
    {
        $appId = $id . '_' . $app->getName();
        $domains = implode(' ', $app->getDomains() ?? throw new ConfigurationException('You must provide at least one domain'));
        $internalPort = $this->portNumberResolver->resolve($id, $index);

        $config = [];
        if ($app->getCache()) {
            $config[] = 'proxy_cache_path /cache/' . $appId . ' keys_zone=' . $appId . ':10m;';
        }
        $config[] = 'server {';
        $config[] = '  server_name ' . $domains . ';';
        $config[] = '  location / {';
        $config[] = '    proxy_pass http://127.0.0.1:' . $internalPort . ';';
        $config[] = '    proxy_set_header X-Forwarded-For $remote_addr;';
        if ($app->getCache()) {
            $config[] = '    proxy_cache ' . $appId . ';';
            $config[] = '    proxy_cache_valid 200 24h;';
        }
        $config[] = '  }';
        $config[] = '}';

        return implode("\n", $config) . "\n";
    }
}
