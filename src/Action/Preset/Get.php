<?php

namespace App\Action\Preset;

use App\Service;
use Fusio\Engine\ActionInterface;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;

readonly class Get implements ActionInterface
{
    public function __construct(private Service\Preset $service)
    {
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): mixed
    {
        return $this->service->load($request->get('name'), $context);
    }
}
