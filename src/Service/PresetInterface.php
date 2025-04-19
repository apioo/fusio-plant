<?php

namespace App\Service;

use App\Model\ProjectApp;

interface PresetInterface
{
    public static function getName(): string;
    public function getDisplayName(): string;

    /**
     * @return array<ProjectApp>
     */
    public function load(): array;
}
