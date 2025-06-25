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

use App\Model;
use PSX\Framework\Config\ConfigInterface;
use PSX\Json\Parser;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

readonly class Executor
{
    private string $inputPipe;
    private string $outputPipe;
    private LockFactory $lockFactory;

    public function __construct(private ConfigInterface $config)
    {
        $this->inputPipe = $this->config->get('plant_pipe_input');
        $this->outputPipe = $this->config->get('plant_pipe_output');
        $this->lockFactory = new LockFactory(new FlockStore($this->config->get('psx_path_cache')));
    }

    public function execute(Model\Command $command): string
    {
        $lock = $this->lockFactory->createLock('command-execute');
        $lock->acquire(true);

        $response = '';

        try {
            $inputHandler = fopen($this->inputPipe, 'w');
            $outputHandler = fopen($this->outputPipe, 'r');

            fwrite($inputHandler, Parser::encode($command) . PHP_EOL);

            while (($buffer = fgets($outputHandler, 4096)) !== false) {
                if (str_contains($buffer, '--PLANT--')) {
                    break;
                }

                $response.= $buffer;
            }

            fclose($inputHandler);
            fclose($outputHandler);
        } finally {
            $lock->release();
        }

        return $response;
    }
}
