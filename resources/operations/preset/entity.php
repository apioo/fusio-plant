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
    $operation->setPublic(true);
    $operation->setDescription('Returns a preset for a specific app');
    $operation->setHttpMethod(HttpMethod::GET);
    $operation->setHttpPath('/preset/:name');
    $operation->setHttpCode(200);
    $operation->setOutgoing(Model\Project::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Preset\Get::class);
};
