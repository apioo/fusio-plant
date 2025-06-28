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

namespace App\Service;

use App\Model;
use PSX\Framework\Config\ConfigInterface;
use PSX\Json\Parser;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

readonly class Executor
{
    private const MAX_TRY = 512;

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
            file_put_contents($this->outputPipe, '');

            $input = fopen($this->inputPipe, 'w');
            fwrite($input, Parser::encode($command, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES) . PHP_EOL);
            fclose($input);

            $output = fopen($this->outputPipe, 'r');
            $count = 0;
            $position = 0;
            while ($count < self::MAX_TRY) {
                fseek($output, $position);
                $response.= fread($output, filesize($this->outputPipe));
                $position = ftell($output);

                if (str_contains($response, '--PLANT--')) {
                    break;
                }

                usleep(200);

                $count++;
            }

            fclose($output);
        } finally {
            file_put_contents($this->outputPipe, '');

            $lock->release();
        }

        return $response;
    }
}
