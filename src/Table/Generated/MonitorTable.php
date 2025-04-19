<?php

namespace App\Table\Generated;

/**
 * @extends \PSX\Sql\TableAbstract<\App\Table\Generated\MonitorRow>
 */
class MonitorTable extends \PSX\Sql\TableAbstract
{
    public const NAME = 'app_monitor';
    public const COLUMN_ID = 'id';
    public const COLUMN_PROJECT_ID = 'project_id';
    public const COLUMN_CONTAINER_ID = 'container_id';
    public const COLUMN_NAME = 'name';
    public const COLUMN_CPU_PERC = 'cpu_perc';
    public const COLUMN_MEM_PERC = 'mem_perc';
    public const COLUMN_MEM_USAGE = 'mem_usage';
    public const COLUMN_MEM_LIMIT = 'mem_limit';
    public const COLUMN_NETIO_RECEIVED = 'netio_received';
    public const COLUMN_NETIO_SENT = 'netio_sent';
    public const COLUMN_BLOCKIO_WRITTEN = 'blockio_written';
    public const COLUMN_BLOCKIO_READ = 'blockio_read';
    public const COLUMN_INSERT_DATE = 'insert_date';
    public function getName(): string
    {
        return self::NAME;
    }
    public function getColumns(): array
    {
        return [self::COLUMN_ID => 0x3020000a, self::COLUMN_PROJECT_ID => 0x20000a, self::COLUMN_CONTAINER_ID => 0xa000ff, self::COLUMN_NAME => 0xa000ff, self::COLUMN_CPU_PERC => 0x500000, self::COLUMN_MEM_PERC => 0x500000, self::COLUMN_MEM_USAGE => 0x20000a, self::COLUMN_MEM_LIMIT => 0x20000a, self::COLUMN_NETIO_RECEIVED => 0x20000a, self::COLUMN_NETIO_SENT => 0x20000a, self::COLUMN_BLOCKIO_WRITTEN => 0x20000a, self::COLUMN_BLOCKIO_READ => 0x20000a, self::COLUMN_INSERT_DATE => 0x800000];
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findAll(?\PSX\Sql\Condition $condition = null, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        return $this->doFindAll($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findBy(\PSX\Sql\Condition $condition, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneBy(\PSX\Sql\Condition $condition): ?\App\Table\Generated\MonitorRow
    {
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function find(int $id): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('id', $id);
        return $this->doFindOneBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findById(int $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('id', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneById(int $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('id', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateById(int $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('id', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteById(int $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('id', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByProjectId(int $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('project_id', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByProjectId(int $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('project_id', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByProjectId(int $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('project_id', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByProjectId(int $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('project_id', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByContainerId(string $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('container_id', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByContainerId(string $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('container_id', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByContainerId(string $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('container_id', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByContainerId(string $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('container_id', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByName(string $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('name', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByName(string $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('name', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByName(string $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('name', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByName(string $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->like('name', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByCpuPerc(string $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('cpu_perc', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByCpuPerc(string $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('cpu_perc', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByCpuPerc(string $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('cpu_perc', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByCpuPerc(string $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('cpu_perc', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByMemPerc(string $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_perc', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByMemPerc(string $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_perc', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByMemPerc(string $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_perc', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByMemPerc(string $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_perc', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByMemUsage(int $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_usage', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByMemUsage(int $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_usage', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByMemUsage(int $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_usage', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByMemUsage(int $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_usage', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByMemLimit(int $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_limit', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByMemLimit(int $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_limit', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByMemLimit(int $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_limit', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByMemLimit(int $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('mem_limit', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByNetioReceived(int $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('netio_received', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByNetioReceived(int $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('netio_received', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByNetioReceived(int $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('netio_received', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByNetioReceived(int $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('netio_received', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByNetioSent(int $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('netio_sent', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByNetioSent(int $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('netio_sent', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByNetioSent(int $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('netio_sent', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByNetioSent(int $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('netio_sent', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByBlockioWritten(int $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('blockio_written', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByBlockioWritten(int $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('blockio_written', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByBlockioWritten(int $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('blockio_written', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByBlockioWritten(int $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('blockio_written', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByBlockioRead(int $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('blockio_read', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByBlockioRead(int $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('blockio_read', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByBlockioRead(int $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('blockio_read', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByBlockioRead(int $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('blockio_read', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @return array<\App\Table\Generated\MonitorRow>
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findByInsertDate(\PSX\DateTime\LocalDateTime $value, ?int $startIndex = null, ?int $count = null, ?\App\Table\Generated\MonitorColumn $sortBy = null, ?\PSX\Sql\OrderBy $sortOrder = null): array
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('insert_date', $value);
        return $this->doFindBy($condition, $startIndex, $count, $sortBy, $sortOrder);
    }
    /**
     * @throws \PSX\Sql\Exception\QueryException
     */
    public function findOneByInsertDate(\PSX\DateTime\LocalDateTime $value): ?\App\Table\Generated\MonitorRow
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('insert_date', $value);
        return $this->doFindOneBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateByInsertDate(\PSX\DateTime\LocalDateTime $value, \App\Table\Generated\MonitorRow $record): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('insert_date', $value);
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteByInsertDate(\PSX\DateTime\LocalDateTime $value): int
    {
        $condition = \PSX\Sql\Condition::withAnd();
        $condition->equals('insert_date', $value);
        return $this->doDeleteBy($condition);
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function create(\App\Table\Generated\MonitorRow $record): int
    {
        return $this->doCreate($record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function update(\App\Table\Generated\MonitorRow $record): int
    {
        return $this->doUpdate($record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function updateBy(\PSX\Sql\Condition $condition, \App\Table\Generated\MonitorRow $record): int
    {
        return $this->doUpdateBy($condition, $record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function delete(\App\Table\Generated\MonitorRow $record): int
    {
        return $this->doDelete($record->toRecord());
    }
    /**
     * @throws \PSX\Sql\Exception\ManipulationException
     */
    public function deleteBy(\PSX\Sql\Condition $condition): int
    {
        return $this->doDeleteBy($condition);
    }
    /**
     * @param array<string, mixed> $row
     */
    protected function newRecord(array $row): \App\Table\Generated\MonitorRow
    {
        return \App\Table\Generated\MonitorRow::from($row);
    }
}