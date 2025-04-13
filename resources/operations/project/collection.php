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
    $operation->setDescription('Returns all available projects');
    $operation->setHttpMethod(HttpMethod::GET);
    $operation->setHttpPath('/project');
    $operation->setHttpCode(200);
    $operation->addParameter('startIndex', PropertyTypeFactory::getInteger());
    $operation->addParameter('count', PropertyTypeFactory::getInteger());
    $operation->addParameter('search', PropertyTypeFactory::getString());
    $operation->setOutgoing(Model\ProjectCollection::class);
    $operation->addThrow(500, Model\ProjectCollection::class);
    $operation->setAction(Action\Project\GetAll::class);
};
