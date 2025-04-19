<?php

namespace App\Service;

use App\Model\ProjectApp;
use App\Model\ProjectAppVolume;
use Fusio\Engine\Inflection\ClassName;
use PSX\Record\Record;

abstract class PresetAbstract implements PresetInterface
{
    public static function getName(): string
    {
        return ClassName::serialize(static::class);
    }

    public function getDisplayName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    protected function newApp(string $name, string $image, ?array $domains = null, ?bool $cache = null, ?int $port = null, ?Record $environment = null, array $volumes = null, array $links = null): ProjectApp
    {
        $app = new ProjectApp();
        $app->setName($name);
        $app->setImage($image);
        $app->setDomains($domains);
        $app->setCache($cache);
        $app->setPort($port);
        $app->setEnvironment($environment);
        $app->setVolumes($volumes);
        $app->setLinks($links);
        return $app;
    }

    protected function newVolume(string $source, string $destination): ProjectAppVolume
    {
        $volume = new ProjectAppVolume();
        $volume->setSource($source);
        $volume->setDestination($destination);
        return $volume;
    }
}
