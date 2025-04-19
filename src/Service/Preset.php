<?php

namespace App\Service;

use App\Model;
use Fusio\Engine\ContextInterface;
use PSX\Http\Exception\NotFoundException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class Preset
{
    /**
     * @var iterable<PresetInterface>
     */
    private iterable $presets;

    public function __construct(#[AutowireIterator('fusio.plant.preset', defaultIndexMethod: 'getName')] iterable $presets)
    {
        $this->presets = $presets;
    }

    /**
     * @return iterable<PresetInterface>
     */
    public function getAll(): iterable
    {
        return $this->presets;
    }

    public function load(string $name, ContextInterface $context): Model\Project
    {
        $apps = $this->getByName($name)->load();

        $project = new Model\Project();
        $project->setApps($apps);
        return $project;
    }

    private function getByName(string $name): PresetInterface
    {
        foreach ($this->presets as $key => $preset) {
            if ($key === $name) {
                return $preset;
            }
        }

        throw new NotFoundException('Provided preset does not exist');
    }
}
