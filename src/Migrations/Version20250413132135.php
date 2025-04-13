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
    }

    public function down(Schema $schema): void
    {
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
