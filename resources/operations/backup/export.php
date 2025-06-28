<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['backup.export']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('Exports a project configuration backup');
    $operation->setHttpMethod(HttpMethod::POST);
    $operation->setHttpPath('/backup/export');
    $operation->setHttpCode(200);
    $operation->setOutgoing(Model\BackupExport::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Backup\Export::class);
};
