<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250303000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create project table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE project (
            id SERIAL NOT NULL, 
            uid VARCHAR(36) NOT NULL,
            name VARCHAR(255) NOT NULL,
            deleted BOOLEAN NOT NULL DEFAULT FALSE,
            PRIMARY KEY(id)
        )');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2FB3D0EE539B0606 ON project (uid)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE project');
    }
}