<?php
/*
 * This file is part of the Fusio Plant project (https://fusio-project.org/product/plant).
 * Fusio Plant is a server control panel to easily self-host apps on your server.
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Service\Project;

use App\Exception\ConfigurationException;
use App\Exception\PortResolveException;
use App\Exception\ProcessTimeoutException;
use App\Model;
use App\Table\Generated\ProjectRow;
use PSX\Json\Parser;

readonly class Worker
{
    public function __construct(private ComposeWriter $composeWriter, private NginxWriter $nginxWriter)
    {
    }

    /**
     * @throws ProcessTimeoutException
     * @throws ConfigurationException
     * @throws PortResolveException
     */
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

    /**
     * @throws ProcessTimeoutException
     */
    public function remove(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandRemove();
        $command->setType('remove');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
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

    /**
     * @throws ProcessTimeoutException
     */
    public function pull(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandPull();
        $command->setType('pull');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function up(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandUp();
        $command->setType('up');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function down(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandDown();
        $command->setType('down');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function logs(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandLogs();
        $command->setType('logs');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function ps(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandPs();
        $command->setType('ps');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function stats(int $id, ProjectRow $project): string
    {
        $commandId = $this->buildCommandId($id);

        $command = new Model\CommandStats();
        $command->setType('stats');
        $command->setName($project->getName());
        $this->writeCommand($commandId, $command);

        return $this->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
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

    /**
     * @throws ProcessTimeoutException
     */
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

            if ($count > 30) {
                throw new ProcessTimeoutException('Command output timeout for: ' . $commandId);
            }
        }
    }

    private function buildCommandId(int $id): string
    {
        return $id . '-' . date('YmdHisv');
    }
}
