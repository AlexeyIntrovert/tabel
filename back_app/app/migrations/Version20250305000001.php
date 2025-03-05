<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250305000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add user profile fields';
    }

    public function up(Schema $schema): void
    {
        // First add columns with NULL allowed
        $this->addSql('ALTER TABLE tabel_user ADD full_name VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE tabel_user ADD position VARCHAR(255) NULL');
        $this->addSql('ALTER TABLE tabel_user ADD grade INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tabel_user ADD production_type VARCHAR(255) NULL');

        // Update existing records with default values
        $this->addSql("UPDATE tabel_user SET full_name = CONCAT('User ', email) WHERE full_name IS NULL");
        $this->addSql("UPDATE tabel_user SET position = 'Not specified' WHERE position IS NULL");
        $this->addSql("UPDATE tabel_user SET production_type = 'Not specified' WHERE production_type IS NULL");

        // Now make columns NOT NULL
        $this->addSql('ALTER TABLE tabel_user ALTER COLUMN full_name SET NOT NULL');
        $this->addSql('ALTER TABLE tabel_user ALTER COLUMN position SET NOT NULL');
        $this->addSql('ALTER TABLE tabel_user ALTER COLUMN production_type SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE tabel_user DROP full_name');
        $this->addSql('ALTER TABLE tabel_user DROP position');
        $this->addSql('ALTER TABLE tabel_user DROP grade');
        $this->addSql('ALTER TABLE tabel_user DROP production_type');
    }
}