<?php

namespace App\Action\Dashboard;

use App\View;
use Fusio\Engine\ActionInterface;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;

readonly class GetAll implements ActionInterface
{
    public function __construct(private View\Dashboard $dashboardView)
    {
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): mixed
    {
        return $this->dashboardView->getCollection();
    }
}
