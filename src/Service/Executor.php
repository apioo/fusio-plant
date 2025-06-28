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
use Psr\Log\LoggerInterface;
use PSX\Framework\Config\ConfigInterface;
use PSX\Json\Parser;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

readonly class Executor
{
    private const MAX_TRY = 512;
    private const EOF_MARKER = '--PLANT-EOF--';

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

            $command = Parser::encode($command, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES) . PHP_EOL;

            $input = fopen($this->inputPipe, 'w');
            fwrite($input, $command);
            fclose($input);

            $output = fopen($this->outputPipe, 'r');
            $count = 0;
            while ($count < self::MAX_TRY) {
                $size = filesize($this->outputPipe);
                if ($size > 0) {
                    $response.= fread($output, $size);
                }

                if (str_contains($response, self::EOF_MARKER)) {
                    $response = str_replace(self::EOF_MARKER, '', $response);
                    $response = trim($response);
                    break;
                }

                usleep(400_000);
                clearstatcache();

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
