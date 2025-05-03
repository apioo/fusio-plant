<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['project']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('Registers an SSL certificate for the provided domain');
    $operation->setHttpMethod(HttpMethod::POST);
    $operation->setHttpPath('/project/:id/certbot');
    $operation->setHttpCode(200);
    $operation->setIncoming(Model\ProjectCertbot::class);
    $operation->setOutgoing(Model\Message::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Project\Certbot::class);
};
