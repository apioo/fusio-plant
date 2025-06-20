<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['execute.logs']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('Returns the latest logs');
    $operation->setHttpMethod(HttpMethod::POST);
    $operation->setHttpPath('/project/:id/execute/logs');
    $operation->setHttpCode(200);
    $operation->setOutgoing(Model\DockerLogs::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Project\Execute\Logs::class);
};
