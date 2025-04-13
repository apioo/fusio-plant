<?php

namespace App\View;

use App\Table;
use Fusio\Impl\Table\Generated\UserTable;
use PSX\Nested\Builder;
use PSX\Nested\Reference;
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
                'id' => $builder->fieldInteger(Table\Generated\ProjectTable::COLUMN_DISPLAY_ID),
                'user' => $builder->doEntity([$this->getTable(UserTable::class), 'find'], [new Reference(Table\Generated\ProjectTable::COLUMN_USER_ID)], [
                    'id' => $builder->fieldInteger(UserTable::COLUMN_ID),
                    'name' => UserTable::COLUMN_NAME,
                ]),
                'name' => Table\Generated\ProjectTable::COLUMN_NAME,
                'updateDate' => $builder->fieldDateTime(Table\Generated\ProjectTable::COLUMN_UPDATE_DATE),
                'insertDate' => $builder->fieldDateTime(Table\Generated\ProjectTable::COLUMN_INSERT_DATE),
                'links' => [
                    'self' => $builder->fieldFormat(Table\Generated\ProjectTable::COLUMN_DISPLAY_ID, '/project/%s'),
                ]
            ]),
        ];

        return $builder->build($definition);
    }

    public function getEntity(string $id): mixed
    {
        $builder = new Builder($this->connection);

        $definition = $builder->doEntity([$this->getTable(Table\Project::class), 'find'], [$id], [
            'id' => $builder->fieldInteger(Table\Generated\ProjectTable::COLUMN_ID),
            'user' => $builder->doEntity([$this->getTable(UserTable::class), 'find'], [new Reference(Table\Generated\ProjectTable::COLUMN_USER_ID)], [
                'id' => $builder->fieldInteger(UserTable::COLUMN_ID),
                'name' => UserTable::COLUMN_NAME,
            ]),
            'name' => Table\Generated\ProjectTable::COLUMN_NAME,
            'apps' => $builder->fieldJson(Table\Generated\ProjectTable::COLUMN_APPS),
            'updateDate' => $builder->fieldDateTime(Table\Generated\ProjectTable::COLUMN_UPDATE_DATE),
            'insertDate' => $builder->fieldDateTime(Table\Generated\ProjectTable::COLUMN_INSERT_DATE),
            'links' => [
                'self' => $builder->fieldFormat(Table\Generated\ProjectTable::COLUMN_DISPLAY_ID, '/project/%s'),
            ]
        ]);

        return $builder->build($definition);
    }
}
