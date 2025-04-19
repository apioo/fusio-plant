<?php

namespace App\Action\Preset;

use App\Service;
use Fusio\Engine\ActionInterface;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;

readonly class GetAll implements ActionInterface
{
    public function __construct(private Service\Preset $service)
    {
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): mixed
    {
        $entries = [];
        $presets = $this->service->getAll();
        foreach ($presets as $name => $preset) {
            $entries[] = [
                'name' => $name,
                'displayName' => $preset->getDisplayName(),
            ];
        }

        return [
            'totalResults' => count($entries),
            'entry' => $entries,
        ];
    }
}
