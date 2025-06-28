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
use App\Service;
use App\Table;
use Fusio\Engine\ContextInterface;
use PSX\Http\Exception\BadRequestException;
use PSX\Json\Parser;
use PSX\Schema\ObjectMapper;
use PSX\Schema\SchemaManagerInterface;
use PSX\Schema\SchemaSource;

readonly class Backup
{
    private ObjectMapper $objectMapper;

    public function __construct(private Service\Project $projectService, private Table\Project $projectTable, private SchemaManagerInterface $schemaManager)
    {
        $this->objectMapper = new ObjectMapper($schemaManager);
    }

    public function export(): Model\BackupExport
    {
        $backup = [];

        $result = $this->projectTable->findAll();
        foreach ($result as $row) {
            $backup[] = [
                'name' => $row->getName(),
                'apps' => Parser::decode($row->getApps()),
            ];
        }

        $export = new Model\BackupExport();
        $export->setExport(Parser::encode($backup));
        return $export;
    }

    public function import(Model\BackupImport $data, ContextInterface $context): void
    {
        $data = Parser::decode($data->getImport() ?? '');
        if (!is_array($data)) {
            throw new BadRequestException('Provided an invalid JSON payload');
        }

        foreach ($data as $row) {
            $project = $this->objectMapper->read($row, SchemaSource::fromClass(Model\Project::class));

            $existingProject = $this->projectTable->findOneByName($row->name);
            if ($existingProject instanceof Table\Generated\ProjectRow) {
                $this->projectService->update($existingProject->getDisplayId(), $project);
            } else {
                $this->projectService->create($project, $context);
            }
        }
    }
}
