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

readonly class Executor
{
    private string $plantPipe;

    public function __construct(private ConfigInterface $config)
    {
        $this->plantPipe = $this->config->get('plant_pipe');
    }

    public function execute(Model\Command $command): string
    {
        $handler = fopen($this->plantPipe, 'w+');
        fwrite($handler, Parser::encode($command));
        fflush($handler);

        $response = '';
        while (($buffer = fgets($handler, 4096)) !== false) {
            if (str_contains($buffer, '--PLANT--')) {
                break;
            }

            $response.= $buffer;
        }

        fclose($handler);

        return $response;
    }
}
