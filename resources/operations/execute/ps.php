<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['execute']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('List containers');
    $operation->setHttpMethod(HttpMethod::POST);
    $operation->setHttpPath('/execute/ps');
    $operation->setHttpCode(200);
    $operation->setOutgoing(Model\DockerProcesses::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Execute\Ps::class);
};
