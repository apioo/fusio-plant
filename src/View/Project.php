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

namespace App\View;

use App\Table;
use PSX\Nested\Builder;
use PSX\Sql\Condition;
use PSX\Sql\ViewAbstract;

class Project extends ViewAbstract
{
    public function getCollection(int $startIndex, int $count, ?string $search = null): mixed
    {
        if (empty($startIndex) || $startIndex < 0) {
            $startIndex = 0;
        }

        if (empty($count) || $count < 1 || $count > 1024) {
            $count = 16;
        }

        $condition = Condition::withAnd();

        if ($search !== null && $search !== '') {
            $condition->like(Table\Generated\ProjectTable::COLUMN_NAME, '%' . $search . '%');
        }

        $builder = new Builder($this->connection);

        $definition = [
            'totalResults' => $this->getTable(Table\Project::class)->getCount($condition),
            'startIndex' => $startIndex,
            'itemsPerPage' => $count,
            'entry' => $builder->doCollection([$this->getTable(Table\Project::class), 'findAll'], [$condition, $startIndex, $count], [
                'id' => Table\Generated\ProjectTable::COLUMN_DISPLAY_ID,
                'name' => Table\Generated\ProjectTable::COLUMN_NAME,
                'updateDate' => $builder->fieldDateTime(Table\Generated\ProjectTable::COLUMN_UPDATE_DATE),
                'insertDate' => $builder->fieldDateTime(Table\Generated\ProjectTable::COLUMN_INSERT_DATE),
            ]),
        ];

        return $builder->build($definition);
    }

    public function getEntity(string $id): mixed
    {
        $builder = new Builder($this->connection);

        $definition = $builder->doEntity([$this->getTable(Table\Project::class), 'findByDisplayId'], [$id], [
            'id' => Table\Generated\ProjectTable::COLUMN_DISPLAY_ID,
            'name' => Table\Generated\ProjectTable::COLUMN_NAME,
            'apps' => $builder->fieldJson(Table\Generated\ProjectTable::COLUMN_APPS),
            'updateDate' => $builder->fieldDateTime(Table\Generated\ProjectTable::COLUMN_UPDATE_DATE),
            'insertDate' => $builder->fieldDateTime(Table\Generated\ProjectTable::COLUMN_INSERT_DATE),
        ]);

        return $builder->build($definition);
    }
}
