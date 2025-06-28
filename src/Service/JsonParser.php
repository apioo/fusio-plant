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

use PSX\Json\Parser;
use PSX\Schema\ObjectMapper;
use PSX\Schema\SchemaManagerInterface;
use PSX\Schema\SchemaSource;

readonly class JsonParser
{
    private ObjectMapper $objectMapper;

    public function __construct(SchemaManagerInterface $schemaManager)
    {
        $this->objectMapper = new ObjectMapper($schemaManager);
    }

    /**
     * @template T
     * @param class-string<T> $class
     * @return array<T>
     */
    public function parseLines(string $output, string $class): array
    {
        $result = [];
        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            try {
                $data = Parser::decode($line);
            } catch (\JsonException) {
                continue;
            }

            if (!$data instanceof \stdClass) {
                continue;
            }

            $result[] = $this->objectMapper->read($data, SchemaSource::fromClass($class));
        }

        return $result;
    }
}
