<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['preset']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('Returns all available presets');
    $operation->setHttpMethod(HttpMethod::GET);
    $operation->setHttpPath('/preset');
    $operation->setHttpCode(200);
    $operation->setOutgoing(Model\PresetCollection::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Preset\GetAll::class);
};
