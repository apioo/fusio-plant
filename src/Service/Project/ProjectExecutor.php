<?php
/*
 * This file is part of the Fusio Plant project (https://github.com/apioo/fusio-plant).
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
use App\Service\Executor;
use App\Table\Generated\ProjectRow;

readonly class ProjectExecutor
{
    public function __construct(private Executor $executor, private ComposeWriter $composeWriter, private NginxWriter $nginxWriter, private BackupCronWriter $backupCronWriter)
    {
    }

    /**
     * @throws ProcessTimeoutException
     * @throws ConfigurationException
     * @throws PortResolveException
     */
    public function setup(int $id, Model\Project $project): string
    {
        $composeYaml = $this->composeWriter->write($id, $project);
        $nginxConfig = $this->nginxWriter->write($id, $project);
        $backupCron = $this->backupCronWriter->write($id, $project);

        $command = new Model\CommandProjectSetup();
        $command->setType('project-setup');
        $command->setName($project->getName());
        $command->setCompose($composeYaml);
        $command->setNginx($nginxConfig);
        $command->setBackup($backupCron);
        return $this->executor->execute($command);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function remove(int $id, ProjectRow $project): string
    {
        $command = new Model\CommandProjectRemove();
        $command->setType('project-remove');
        $command->setName($project->getName());
        return $this->executor->execute($command);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function pull(int $id, ProjectRow $project): string
    {
        $command = new Model\CommandProjectPull();
        $command->setType('project-pull');
        $command->setName($project->getName());
        return $this->executor->execute($command);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function up(int $id, ProjectRow $project): string
    {
        $command = new Model\CommandProjectUp();
        $command->setType('project-up');
        $command->setName($project->getName());
        return $this->executor->execute($command);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function deploy(int $id, ProjectRow $project): string
    {
        $command = new Model\CommandProjectDeploy();
        $command->setType('project-deploy');
        $command->setName($project->getName());
        return $this->executor->execute($command);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function down(int $id, ProjectRow $project): string
    {
        $command = new Model\CommandProjectDown();
        $command->setType('project-down');
        $command->setName($project->getName());
        return $this->executor->execute($command);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function logs(int $id, ProjectRow $project): string
    {
        $command = new Model\CommandProjectLogs();
        $command->setType('project-logs');
        $command->setName($project->getName());
        return $this->executor->execute($command);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function ps(int $id, ProjectRow $project): string
    {
        $command = new Model\CommandProjectPs();
        $command->setType('project-ps');
        $command->setName($project->getName());
        return $this->executor->execute($command);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function stats(int $id, ProjectRow $project): string
    {
        $command = new Model\CommandProjectStats();
        $command->setType('project-stats');
        $command->setName($project->getName());
        return $this->executor->execute($command);
    }
}
