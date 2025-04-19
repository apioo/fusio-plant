<?php

namespace App\Action\Monitor;

use App\Service;
use Fusio\Engine\ActionInterface;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;

readonly class FetchUsage implements ActionInterface
{
    public function __construct(private Service\Monitor $service)
    {
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): mixed
    {
        $this->service->fetchUsage();

        return [
            'success' => true,
            'message' => 'Fetched usage successfully',
        ];
    }
}
