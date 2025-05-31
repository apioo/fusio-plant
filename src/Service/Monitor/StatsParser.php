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

namespace App\Service\Monitor;

use App\DTO\Docker\Stats;
use App\Model;

readonly class StatsParser
{
    /**
     * @return \Generator<Stats>
     */
    public function parse(Model\DockerStatistics $stats): \Generator
    {
        foreach ($stats->getEntry() as $entry) {
            [$memUsage, $memLimit] = $this->parseUnits($entry->getMemUsage());
            [$netIOReceived, $netIOSent] = $this->parseUnits($entry->getBlockIO());
            [$blockIOWritten, $blockIORead] = $this->parseUnits($entry->getNetIO());

            yield new Stats(
                $entry->getContainer(),
                $entry->getName(),
                $this->parsePercentage($entry->getCPUPerc()),
                $this->parsePercentage($entry->getMemPerc()),
                $memUsage,
                $memLimit,
                $netIOReceived,
                $netIOSent,
                $blockIOWritten,
                $blockIORead,
            );
        }
    }

    private function parsePercentage(?string $data): int
    {
        if (empty($data)) {
            return 0;
        }

        return (int) (rtrim(trim($data), '%') * 100);
    }

    private function parseUnits(?string $data): array
    {
        if (empty($data)) {
            return [0, 0];
        }

        $parts = explode('/', $data, 2);

        return [
            $this->parseUnit($parts[0] ?? null),
            $this->parseUnit($parts[1] ?? null)
        ];
    }

    /**
     * Convert all units to KiB
     */
    private function parseUnit(?string $data): int
    {
        if (empty($data)) {
            return 0;
        }

        $data = strtolower(trim($data));

        $bytes = 0;
        if (str_ends_with($data, 'gb')) {
            $bytes = (int) (substr($data, 0, -2) * 1000 * 1000 * 1000);
        } elseif (str_ends_with($data, 'gib')) {
            $bytes = (int) (substr($data, 0, -3) * 1024 * 1024 * 1024);
        } elseif (str_ends_with($data, 'mb')) {
            $bytes = (int) (substr($data, 0, -2) * 1000 * 1000);
        } elseif (str_ends_with($data, 'mib')) {
            $bytes = (int) (substr($data, 0, -3) * 1024 * 1024);
        } elseif (str_ends_with($data, 'kb')) {
            $bytes = (int) (substr($data, 0, -2) * 1000);
        } elseif (str_ends_with($data, 'kib')) {
            $bytes = (int) (substr($data, 0, -3) * 1024);
        } elseif (str_ends_with($data, 'b')) {
            $bytes = (int) (substr($data, 0, -1));
        }

        return $bytes > 0 ? $bytes / 1024 : 0;
    }
}
