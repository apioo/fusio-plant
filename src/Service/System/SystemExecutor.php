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

namespace App\Service\System;

use App\Exception\ProcessTimeoutException;
use App\Model;
use App\Service\Executor;

readonly class SystemExecutor
{
    public function __construct(private Executor $executor)
    {
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function certbot(Model\CertbotRequest $request): string
    {
        $commandId = $this->buildCommandId(0);

        $command = new Model\CommandCertbot();
        $command->setType('certbot');
        $command->setDomain($request->getDomain());
        $command->setEmail($request->getEmail());
        $this->executor->writeCommand($commandId, $command);

        return $this->executor->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function images(): string
    {
        $commandId = $this->buildCommandId(0);

        $command = new Model\CommandPs();
        $command->setType('images');
        $this->executor->writeCommand($commandId, $command);

        return $this->executor->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function login(Model\DockerLogin $login): string
    {
        $commandId = $this->buildCommandId(0);

        $command = new Model\CommandLogin();
        $command->setType('login');
        $command->setDomain($login->getDomain());
        $command->setUsername($login->getUsername());
        $command->setPassword($login->getPassword());
        $this->executor->writeCommand($commandId, $command);

        return $this->executor->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function ps(): string
    {
        $commandId = $this->buildCommandId(0);

        $command = new Model\CommandPs();
        $command->setType('ps');
        $this->executor->writeCommand($commandId, $command);

        return $this->executor->waitForResponse($commandId);
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function stats(): string
    {
        $commandId = $this->buildCommandId(0);

        $command = new Model\CommandStats();
        $command->setType('stats');
        $this->executor->writeCommand($commandId, $command);

        return $this->executor->waitForResponse($commandId);
    }

    private function buildCommandId(int $id): string
    {
        return $id . '-' . uniqid() . '-' . date('YmdHisv');
    }
}
