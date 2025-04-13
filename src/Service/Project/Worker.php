<?php

namespace App\Service\Project;

use App\Model;
use App\Table\Generated\ProjectRow;
use PSX\Json\Parser;

readonly class Worker
{
    public function __construct(private ComposeWriter $composeWriter, private NginxWriter $nginxWriter)
    {
    }

    public function setup(int $id, Model\Project $project): void
    {
        $composeYaml = $this->composeWriter->write($id, $project->getApps());
        $nginxConfig = $this->nginxWriter->write($id, $project->getApps());

        $commandId = $id . '-' . date('YmdHisv') . '.cmd';

        $command = new Model\CommandSetup();
        $command->setType('setup');
        $command->setName($project->getName());
        $command->setCompose($composeYaml);
        $command->setNginx($nginxConfig);
        $this->writeCommand($commandId, $command);

        $response = $this->waitForResponse($commandId);
    }

    public function update(int $id, Model\Project $project): void
    {

    }

    public function remove(int $id, ProjectRow $project): void
    {

    }

    private function writeCommand(string $commandId, Model\Command $command): void
    {
        file_put_contents(__DIR__ . '/../../../input/' . $commandId . '.cmd', Parser::encode($command));
    }

    private function waitForResponse(string $commandId): string
    {
        $file = __DIR__ . '/../../../output/' . $commandId . '.cmd';

        $count = 0;
        while (true) {
            if (is_file($file)) {
                return file_get_contents($file);
            }

            sleep(1);
            $count++;

            if ($count > 60) {
                throw new \RuntimeException('Command output timeout for: ' . $commandId);
            }
        }
    }
}
