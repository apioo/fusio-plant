<?php

namespace App\Action\Project;

use App\View;
use Fusio\Engine\ActionInterface;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;

class GetAll implements ActionInterface
{
    public function __construct(
        private View\Project $view
    )
    {
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): mixed
    {
        return $this->view->getCollection(
            (int)$request->get('startIndex'),
            (int)$request->get('count'),
            $request->get('search'),
        );
    }
}
