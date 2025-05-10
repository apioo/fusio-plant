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

use App\Exception\PortResolveException;

class PortNumberResolver
{
    /**
     * @throws PortResolveException
     */
    public function resolve(int $id, int $index): int
    {
        if ($index > 9) {
            throw new PortResolveException('The max allowed number of services in one project is 10, got: ' . $index);
        }

        if ($id < 100) {
            $internalPort = '9' . str_pad('' . $id, 2, '0', \STR_PAD_LEFT) . $index;
        } elseif ($id < 1000) {
            $internalPort = '1' . str_pad('' . $id, 3, '0', \STR_PAD_LEFT) . $index;
        } else {
            $id = $id + 1000;
            $internalPort = $id . $index;
        }

        $port = (int) $internalPort;
        if ($port > 65_535) {
            throw new PortResolveException('Reached max port range of ' . 65_535);
        }

        return $port;
    }
}
