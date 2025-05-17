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

use App\Service\Monitor\StatsParser;
use App\Table;
use PSX\DateTime\LocalDateTime;

readonly class Monitor
{
    public function __construct(private Project $project, private Table\Project $projectTable, private Table\Monitor $monitorTable, private StatsParser $statsParser)
    {
    }

    public function fetchUsage(): void
    {
        $projects = $this->projectTable->findAll();
        foreach ($projects as $project) {
            $response = $this->project->stats($project->getDisplayId());
            $this->parseOutput($project, $response->getOutput());
        }
    }

    private function parseOutput(Table\Generated\ProjectRow $project, ?string $output): void
    {
        if (empty($output)) {
            return;
        }

        $result = $this->statsParser->parse($output);
        foreach ($result as $stats) {
            $row = new Table\Generated\MonitorRow();
            $row->setProjectId($project->getId());
            $row->setContainerId($stats->container);
            $row->setName($stats->name);
            $row->setCpuPerc($stats->cpuPercentage);
            $row->setMemPerc($stats->memPercentage);
            $row->setMemUsage($stats->memUsage);
            $row->setMemLimit($stats->memLimit);
            $row->setNetioReceived($stats->netIOReceived);
            $row->setNetioSent($stats->netIOSent);
            $row->setBlockioWritten($stats->blockIOWritten);
            $row->setBlockioRead($stats->blockIORead);
            $row->setInsertDate(LocalDateTime::now());
            $this->monitorTable->create($row);
        }
    }
}
