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

use App\Service\Munin\TimeUnit;
use PSX\Http\Exception as StatusCode;

readonly class Munin
{
    private const string PATH = '/var/cache/munin/www';

    public function getDisk(TimeUnit $timeUnit): string
    {
        $html = file_get_contents(self::PATH . '/disk-' . $timeUnit->value . '.html');
        if ($html === false) {
            throw new StatusCode\InternalServerErrorException('Could not fetch disk report');
        }

        return $this->extractBody($html);
    }

    public function getNetwork(TimeUnit $timeUnit): string
    {
        $html = file_get_contents(self::PATH . '/network-' . $timeUnit->value . '.html');
        if ($html === false) {
            throw new StatusCode\InternalServerErrorException('Could not fetch network report');
        }

        return $this->extractBody($html);
    }

    public function getProcesses(TimeUnit $timeUnit): string
    {
        $html = file_get_contents(self::PATH . '/processes-' . $timeUnit->value . '.html');
        if ($html === false) {
            throw new StatusCode\InternalServerErrorException('Could not fetch processes report');
        }

        return $this->extractBody($html);
    }

    public function getRadio(TimeUnit $timeUnit): string
    {
        $html = file_get_contents(self::PATH . '/radio-' . $timeUnit->value . '.html');
        if ($html === false) {
            throw new StatusCode\InternalServerErrorException('Could not fetch radio report');
        }

        return $this->extractBody($html);
    }

    public function getSystem(TimeUnit $timeUnit): string
    {
        $html = file_get_contents(self::PATH . '/system-' . $timeUnit->value . '.html');
        if ($html === false) {
            throw new StatusCode\InternalServerErrorException('Could not fetch system report');
        }

        return $this->extractBody($html);
    }

    private function extractBody(string $html): string
    {
        $pattern = '/<body[^>]*>(.*?)<\/body>/si';
        if (preg_match($pattern, $html, $matches)) {
            return trim($matches[1]);
        }

        throw new StatusCode\InternalServerErrorException('Could not fetch report');
    }
}
