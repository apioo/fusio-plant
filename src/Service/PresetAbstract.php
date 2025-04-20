<?php

namespace App\Service;

use App\Model\ProjectApp;
use App\Model\ProjectAppVolume;
use Fusio\Engine\Inflection\ClassName;
use PSX\Record\Record;
use Symfony\Component\DependencyInjection\Container;

abstract class PresetAbstract implements PresetInterface
{
    public static function getName(): string
    {
        return ClassName::serialize(static::class);
    }

    public function getDisplayName(): string
    {
        return str_replace(' ', '-', ucwords(str_replace('_', ' ', Container::underscore((new \ReflectionClass($this))->getShortName()))));
    }

    protected function newApp(string $name, string $image, ?array $domains = null, ?bool $cache = null, ?int $port = null, ?Record $environment = null, array $links = null, array $volumes = null): ProjectApp
    {
        $app = new ProjectApp();
        $app->setName($name);
        $app->setImage($image);
        $app->setDomains($domains ?? []);
        $app->setCache($cache);
        $app->setPort($port);
        $app->setEnvironment($environment ?? new Record());
        $app->setVolumes($volumes ?? []);
        $app->setLinks($links ?? []);
        return $app;
    }

    /**
     * @param string $source - The source of the mount. For named volumes, this is the name of the volume. For anonymous volumes, this field is omitted.
     * @param string $destination - The path where the file or directory is mounted in the container.
     */
    protected function newVolume(string $source, string $destination): ProjectAppVolume
    {
        $volume = new ProjectAppVolume();
        $volume->setSource($source);
        $volume->setDestination($destination);
        return $volume;
    }

    protected function addLink(?ProjectApp $app, string $link): void
    {
        if (!$app instanceof ProjectApp) {
            return;
        }

        $app->setLinks(array_merge($app->getLinks(), [$link]));
    }
}
