<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;
use PSX\Schema\Type\Factory\PropertyTypeFactory;

return function (Operation $operation) {
    $operation->setScopes(['project']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('Removes a specific page');
    $operation->setHttpMethod(HttpMethod::DELETE);
    $operation->setHttpPath('/project/:id');
    $operation->setHttpCode(200);
    $operation->setOutgoing(Model\Message::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Project\Delete::class);
};
