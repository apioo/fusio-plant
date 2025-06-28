<?php

use App\Action;
use App\Model;
use Fusio\Cli\Builder\Operation;
use Fusio\Cli\Builder\Operation\HttpMethod;
use Fusio\Cli\Builder\Operation\Stability;

return function (Operation $operation) {
    $operation->setScopes(['backup.import']);
    $operation->setStability(Stability::EXPERIMENTAL);
    $operation->setPublic(false);
    $operation->setDescription('Imports a project configuration backup');
    $operation->setHttpMethod(HttpMethod::POST);
    $operation->setHttpPath('/backup/import');
    $operation->setHttpCode(200);
    $operation->setIncoming(Model\BackupImport::class);
    $operation->setOutgoing(Model\Message::class);
    $operation->addThrow(999, Model\Message::class);
    $operation->setAction(Action\Backup\Import::class);
};
