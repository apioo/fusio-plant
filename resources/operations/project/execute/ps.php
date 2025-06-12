<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['execute.ps']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('List containers for the project');
    $operation->setHttpMethod(HttpMethod::POST);
    $operation->setHttpPath('/project/:id/execute/ps');
    $operation->setHttpCode(200);
    $operation->setOutgoing(Model\DockerProcesses::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Project\Execute\Ps::class);
};
