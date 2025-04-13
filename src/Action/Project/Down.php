<?php

namespace App\Action\Project;

use App\Service;
use Fusio\Engine\ActionInterface;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;

readonly class Down implements ActionInterface
{
    public function __construct(private Service\Project $service)
    {
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): mixed
    {
        return $this->service->down($request->get('id'));
    }
}
