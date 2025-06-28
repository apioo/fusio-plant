<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250413132135 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $projectTable = $schema->createTable('app_project');
        $projectTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $projectTable->addColumn('user_id', 'integer', ['notnull' => false]);
        $projectTable->addColumn('display_id', 'guid');
        $projectTable->addColumn('name', 'string');
        $projectTable->addColumn('apps', 'text');
        $projectTable->addColumn('update_date', 'datetime');
        $projectTable->addColumn('insert_date', 'datetime');
        $projectTable->setPrimaryKey(['id']);
        $projectTable->addUniqueIndex(['display_id']);
        $projectTable->addUniqueIndex(['name']);

        $monitorTable = $schema->createTable('app_monitor');
        $monitorTable->addColumn('id', 'integer', ['autoincrement' => true]);
        $monitorTable->addColumn('project_id', 'integer');
        $monitorTable->addColumn('container_id', 'string');
        $monitorTable->addColumn('name', 'string');
        $monitorTable->addColumn('cpu_perc', 'integer');
        $monitorTable->addColumn('mem_perc', 'integer');
        $monitorTable->addColumn('mem_usage', 'integer');
        $monitorTable->addColumn('mem_limit', 'integer');
        $monitorTable->addColumn('netio_received', 'integer');
        $monitorTable->addColumn('netio_sent', 'integer');
        $monitorTable->addColumn('blockio_written', 'integer');
        $monitorTable->addColumn('blockio_read', 'integer');
        $monitorTable->addColumn('insert_date', 'datetime');
        $monitorTable->setPrimaryKey(['id']);

        $monitorTable->addForeignKeyConstraint($schema->getTable('app_project'), ['project_id'], ['id'], [], 'monitor_project_id');
    }

    public function down(Schema $schema): void
    {
        $schema->dropTable('app_project');
        $schema->dropTable('app_monitor');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
