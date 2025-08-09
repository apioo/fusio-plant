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

namespace App\View;

use App\Table;
use Fusio\Model\Backend\StatisticChart;
use Fusio\Model\Backend\StatisticChartSeries;
use PSX\Sql\ViewAbstract;

class Dashboard extends ViewAbstract
{
    public function getCollection(): mixed
    {
        return [
            'cpuPerc' => $this->getStats('cpu_perc'),
            'memPerc' => $this->getStats('mem_perc'),
            'netioReceived' => $this->getStats('netio_received'),
            'netioSent' => $this->getStats('netio_sent'),
            'blockioWritten' => $this->getStats('blockio_written'),
            'blockioRead' => $this->getStats('blockio_read'),
        ];
    }

    private function getStats(string $column): StatisticChart
    {
        $fromDate = new \DateTimeImmutable();
        $fromDate = $fromDate->sub(new \DateInterval('P14D'));
        $toDate = new \DateTimeImmutable();

        $projects = $this->getTable(Table\Project::class)->findAll();
        $data = [];
        $series = [];

        foreach ($projects as $project) {
            $data[$project->getId()] = [];
            $series[$project->getId()] = $project->getName();

            $projectFromDate = $fromDate;
            while ($projectFromDate <= $toDate) {
                $data[$project->getId()][$projectFromDate->format('Y-m-d')] = 0;

                $projectFromDate = $projectFromDate->add(new \DateInterval('P1D'));
            }
        }

        foreach ($projects as $project) {
            $sql = 'SELECT AVG(mon.' . $column . ') AS val,
                           mon.project_id,
                           DATE(mon.insert_date) AS date
                      FROM app_monitor mon
                     WHERE mon.project_id = :project_id
                  GROUP BY DATE(mon.insert_date), mon.project_id';

            $result = $this->connection->fetchAllAssociative($sql, ['project_id' => $project->getId()]);

            foreach ($result as $row) {
                $projectId = (int) $row['project_id'];
                if (isset($data[$projectId][$row['date']])) {
                    $data[$projectId][$row['date']] = (int) $row['val'];
                }
            }
        }

        // build labels
        $diff = $toDate->getTimestamp() - $fromDate->getTimestamp();
        $labels = [];
        $labelFromDate = $fromDate;
        while ($labelFromDate <= $toDate) {
            $labels[] = $labelFromDate->format($diff < 2419200 ? 'D' : 'Y-m-d');

            $labelFromDate = $labelFromDate->add(new \DateInterval('P1D'));
        }

        return $this->build($data, $series, $labels);
    }

    private function build(array $data, array $seriesNames, array $labels): StatisticChart
    {
        $allSeries = [];
        foreach ($seriesNames as $key => $name) {
            $series = new StatisticChartSeries();
            $series->setName($name);
            $series->setData(array_values($data[$key] ?? []));
            $allSeries[] = $series;
        }

        $chart = new StatisticChart();
        $chart->setLabels($labels);
        $chart->setSeries($allSeries);
        return $chart;
    }
}
