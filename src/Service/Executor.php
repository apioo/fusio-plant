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

namespace App\Service;

use App\Exception\ProcessTimeoutException;
use App\Model;
use PSX\Json\Parser;

readonly class Executor
{
    public function writeCommand(string $commandId, Model\Command $command): void
    {
        file_put_contents(__DIR__ . '/../../../input/' . $commandId . '.cmd', Parser::encode($command));
    }

    /**
     * @throws ProcessTimeoutException
     */
    public function waitForResponse(string $commandId): string
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
}
