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

    public function setup(int $id, Model\Project $project): string
    {
        $composeYaml = $this->composeWriter->write($id, $project->getApps());
        $nginxConfig = $this->nginxWriter->write($id, $project->getApps());

        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandSetup();
        $command->setType('setup');
        $command->setName($project->getName());
        $command->setCompose($composeYaml);
        $command->setNginx($nginxConfig);
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    public function remove(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandRemove();
        $command->setType('remove');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    public function certbot(int $id, Model\ProjectCertbot $certbot): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandCertbot();
        $command->setType('certbot');
        $command->setDomain($certbot->getDomain());
        $command->setEmail($certbot->getEmail());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    public function pull(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandPull();
        $command->setType('pull');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    public function up(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandUp();
        $command->setType('up');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    public function down(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandDown();
        $command->setType('down');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    public function logs(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandLogs();
        $command->setType('logs');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    public function ps(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandPs();
        $command->setType('ps');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    public function stats(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandStats();
        $command->setType('stats');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    public function login(int $id, string $username, string $password): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandLogin();
        $command->setType('login');
        $command->setUsername($username);
        $command->setPassword($password);
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
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

    private function buildCommandId(int $id): string
    {
        return $id . '-' . date('YmdHisv') . '.cmd';
    }
}
