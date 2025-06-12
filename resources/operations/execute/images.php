<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['execute.images']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('Returns all available images');
    $operation->setHttpMethod(HttpMethod::POST);
    $operation->setHttpPath('/execute/images');
    $operation->setHttpCode(200);
    $operation->setOutgoing(Model\DockerImages::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Execute\Images::class);
};
