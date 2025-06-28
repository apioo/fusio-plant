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

use App\DTO\Docker\Stats;
use App\Model;
use App\Service\JsonParser;
use App\Service\Monitor\StatsParser;
use PHPUnit\Framework\TestCase;
use PSX\Schema\SchemaManager;

class StatsParserTest extends TestCase
{
    public function testParse()
    {
        $raw = <<<TEXT
{"BlockIO":"0B / 4.1kB","CPUPerc":"0.00%","Container":"11f38036482f","ID":"11f38036482f","MemPerc":"0.69%","MemUsage":"54.32MiB / 7.709GiB","Name":"drupal-drupal-1","NetIO":"5.98kB / 19.3kB","PIDs":"7"}
{"BlockIO":"0B / 418kB","CPUPerc":"0.00%","Container":"82b7cd0aaf17","ID":"82b7cd0aaf17","MemPerc":"0.24%","MemUsage":"19.14MiB / 7.709GiB","Name":"drupal-postgres-1","NetIO":"1.84kB / 126B","PIDs":"6"}
TEXT;

        $jsonParser = new JsonParser(new SchemaManager());
        $lines = $jsonParser->parseLines($raw, Model\DockerStatistic::class);

        $collection = new Model\DockerStatistics();
        $collection->setTotalResults(count($lines));
        $collection->setEntry($lines);

        $result = iterator_to_array((new StatsParser())->parse($collection));

        self::assertCount(2, $result);

        $stats = $result[0] ?? null;

        self::assertInstanceOf(Stats::class, $stats);
        self::assertSame('11f38036482f', $stats->container);
        self::assertSame('drupal-drupal-1', $stats->name);
        self::assertSame(0, $stats->cpuPercentage);
        self::assertSame(69, $stats->memPercentage);
        self::assertSame(55623, $stats->memUsage);
        self::assertSame(8083472, $stats->memLimit);
        self::assertSame(0, $stats->netIOReceived);
        self::assertSame(4, $stats->netIOSent);
        self::assertSame(5, $stats->blockIOWritten);
        self::assertSame(18, $stats->blockIORead);
    }
}
