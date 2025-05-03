<?php

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
        $fromDate = new \DateTime();
        $fromDate->sub(new \DateInterval('P14D'));
        $toDate = new \DateTime();

        $projects = $this->getTable(Table\Project::class)->findAll();
        $data = [];
        $series = [];

        foreach ($projects as $project) {
            $data[$project->getId()] = [];
            $series[$project->getId()] = $project->getName();

            while ($fromDate <= $toDate) {
                $data[$project->getId()][$fromDate->format('Y-m-d')] = 0;

                $fromDate = $fromDate->add(new \DateInterval('P1D'));
            }
        }

        foreach ($projects as $project) {
            $sql = 'SELECT AVG(mon.' . $column . ') AS val,
                           mon.project_id,
                           DATE(mon.insert_date) AS date
                      FROM app_monitor mon
                     WHERE mon.project_id = :project_id
                  GROUP BY DATE(mon.insert_date), mon.operation_id';

            $result = $this->connection->fetchAllAssociative($sql, ['project_id' => $project->getId()]);

            foreach ($result as $row) {
                if (isset($data[$row['project_id']][$row['date']])) {
                    $data[$row['project_id']][$row['date']] = (int) $row['val'];
                }
            }
        }

        // build labels
        $diff = $toDate->getTimestamp() - $fromDate->getTimestamp();
        $labels = [];
        while ($fromDate <= $toDate) {
            $labels[] = $fromDate->format($diff < 2419200 ? 'D' : 'Y-m-d');

            $fromDate = $fromDate->add(new \DateInterval('P1D'));
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
