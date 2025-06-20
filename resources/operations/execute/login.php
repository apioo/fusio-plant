<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['execute.login']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('Docker login to pull private images');
    $operation->setHttpMethod(HttpMethod::POST);
    $operation->setHttpPath('/execute/login');
    $operation->setHttpCode(200);
    $operation->setIncoming(Model\DockerLogin::class);
    $operation->setOutgoing(Model\Message::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Execute\Login::class);
};
