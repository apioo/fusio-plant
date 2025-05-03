<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['dashboard']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('Returns all dashboard statistics');
    $operation->setHttpMethod(HttpMethod::GET);
    $operation->setHttpPath('/dashboard');
    $operation->setHttpCode(200);
    $operation->setOutgoing(Model\DashboardCollection::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Preset\GetAll::class);
};
