<?php

namespace App\Service;

use App\Model;
use App\Model\Message;
use App\Service\Project\Worker;
use App\Table;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\DispatcherInterface;
use PSX\CloudEvents\Builder;
use PSX\DateTime\LocalDateTime;
use PSX\Http\Exception as StatusCode;
use PSX\Json\Parser;
use Ramsey\Uuid\Uuid;

readonly class Monitor
{
    public function __construct(private Project $project, private Table\Project $projectTable, private Table\Monitor $monitorTable)
    {
    }

    public function fetchUsage(): void
    {
        $projects = $this->projectTable->findAll();
        foreach ($projects as $project) {
            $response = $this->project->stats($project->getId());
            $this->parseOutput($project, $response->getOutput());
        }
    }

    private function parseOutput(Table\Generated\ProjectRow $project, ?string $output): void
    {
        if (empty($output)) {
            return;
        }

        $lines = explode("\n", $output);
        foreach ($lines as $line) {
            $data = Parser::decode(trim($line));
            if (!$data instanceof \stdClass) {
                continue;
            }

            $container = $data->Container ?? null;
            $name = $data->Name ?? null;
            $cpuPercentage = $this->parsePercentage($data->CPUPerc ?? null);
            $memPercentage = $this->parsePercentage($data->MemPerc ?? null);
            [$memUsage, $memLimit] = $this->parseUnits($data->MemUsage ?? null);
            [$netIOReceived, $netIOSent] = $this->parseUnits($data->BlockIO ?? null);
            [$blockIOWritten, $blockIORead] = $this->parseUnits($data->NetIO ?? null);

            $row = new Table\Generated\MonitorRow();
            $row->setProjectId($project->getId());
            $row->setContainerId($container);
            $row->setName($name);
            $row->setCpuPerc($cpuPercentage);
            $row->setMemPerc($memPercentage);
            $row->setMemUsage($memUsage);
            $row->setMemLimit($memLimit);
            $row->setNetioReceived($netIOReceived);
            $row->setNetioSent($netIOSent);
            $row->setBlockioWritten($blockIOWritten);
            $row->setBlockioRead($blockIORead);
            $row->setInsertDate(LocalDateTime::now());
            $this->monitorTable->create($row);
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
