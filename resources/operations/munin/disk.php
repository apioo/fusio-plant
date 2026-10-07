<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['munin']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('Returns a munin disk report');
    $operation->setHttpMethod(HttpMethod::GET);
    $operation->setHttpPath('/munin/disk/:time_unit');
    $operation->setHttpCode(200);
    $operation->setOutgoing(Model\MuninReport::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Munin\GetDisk::class);
};
