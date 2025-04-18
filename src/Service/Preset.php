<?php

namespace App\Service;

use App\Model;
use Fusio\Engine\ContextInterface;

readonly class Preset
{
    public function __construct()
    {
    }

    public function getAll(): array
    {
        return [];
    }

    public function load(string $name, ContextInterface $context): Model\Project
    {
        $apps = [];

        $project = new Model\Project();
        $project->setApps($apps);
        return $project;
    }
}
