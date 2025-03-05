<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250305185333 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Create new tables
        $this->addSql('CREATE SEQUENCE tabel_groups_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tabel_positions_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE tabel_production_types_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        
        $this->addSql('CREATE TABLE tabel_groups (id INT NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D5391C377153098 ON tabel_groups (code)');
        $this->addSql('CREATE TABLE tabel_positions (id INT NOT NULL, name VARCHAR(255) NOT NULL, grade INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE tabel_production_types (id INT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');

        // Insert default values
        $this->addSql("INSERT INTO tabel_groups (id, name, code) VALUES (nextval('tabel_groups_id_seq'), 'Default Group', 'DEFAULT')");
        $this->addSql("INSERT INTO tabel_positions (id, name, grade) VALUES (nextval('tabel_positions_id_seq'), 'Default Position', NULL)");
        $this->addSql("INSERT INTO tabel_production_types (id, name) VALUES (nextval('tabel_production_types_id_seq'), 'Default Type')");

        // Add new columns as nullable first
        $this->addSql('ALTER TABLE tabel_user ADD position_id INT NULL');
        $this->addSql('ALTER TABLE tabel_user ADD production_type_id INT NULL');
        $this->addSql('ALTER TABLE tabel_user ADD group_id INT NULL');

        // Update existing records with default values
        $this->addSql('UPDATE tabel_user SET position_id = (SELECT id FROM tabel_positions LIMIT 1)');
        $this->addSql('UPDATE tabel_user SET production_type_id = (SELECT id FROM tabel_production_types LIMIT 1)');
        $this->addSql('UPDATE tabel_user SET group_id = (SELECT id FROM tabel_groups LIMIT 1)');

        // Now make columns NOT NULL
        $this->addSql('ALTER TABLE tabel_user ALTER COLUMN position_id SET NOT NULL');
        $this->addSql('ALTER TABLE tabel_user ALTER COLUMN production_type_id SET NOT NULL');
        $this->addSql('ALTER TABLE tabel_user ALTER COLUMN group_id SET NOT NULL');

        // Add foreign key constraints
        $this->addSql('ALTER TABLE tabel_user ADD CONSTRAINT FK_75D28299DD842E46 FOREIGN KEY (position_id) REFERENCES tabel_positions (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tabel_user ADD CONSTRAINT FK_75D28299D059014E FOREIGN KEY (production_type_id) REFERENCES tabel_production_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE tabel_user ADD CONSTRAINT FK_75D28299FE54D947 FOREIGN KEY (group_id) REFERENCES tabel_groups (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        // Create indexes
        $this->addSql('CREATE INDEX IDX_75D28299DD842E46 ON tabel_user (position_id)');
        $this->addSql('CREATE INDEX IDX_75D28299D059014E ON tabel_user (production_type_id)');
        $this->addSql('CREATE INDEX IDX_75D28299FE54D947 ON tabel_user (group_id)');

        // Drop old columns
        $this->addSql('ALTER TABLE tabel_user DROP "position"');
        $this->addSql('ALTER TABLE tabel_user DROP production_type');

        // Other schema changes
        $this->addSql('ALTER TABLE project ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE project ALTER deleted DROP DEFAULT');
        $this->addSql('ALTER TABLE tabel_user ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE tabel_user ALTER tab_num SET DEFAULT 0');
        $this->addSql('ALTER TABLE tabel_user ALTER gr_kod SET DEFAULT 0');
        $this->addSql('ALTER INDEX uniq_8d93d649e7927c74 RENAME TO UNIQ_75D28299E7927C74');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE tabel_user DROP CONSTRAINT FK_75D28299FE54D947');
        $this->addSql('ALTER TABLE tabel_user DROP CONSTRAINT FK_75D28299DD842E46');
        $this->addSql('ALTER TABLE tabel_user DROP CONSTRAINT FK_75D28299D059014E');
        $this->addSql('DROP SEQUENCE tabel_groups_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tabel_positions_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE tabel_production_types_id_seq CASCADE');
        $this->addSql('DROP TABLE tabel_groups');
        $this->addSql('DROP TABLE tabel_positions');
        $this->addSql('DROP TABLE tabel_production_types');
        $this->addSql('DROP INDEX IDX_75D28299DD842E46');
        $this->addSql('DROP INDEX IDX_75D28299D059014E');
        $this->addSql('DROP INDEX IDX_75D28299FE54D947');
        $this->addSql('ALTER TABLE tabel_user ADD "position" VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tabel_user ADD production_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE tabel_user DROP position_id');
        $this->addSql('ALTER TABLE tabel_user DROP production_type_id');
        $this->addSql('ALTER TABLE tabel_user DROP group_id');
        $this->addSql('CREATE SEQUENCE tabel_user_id_seq');
        $this->addSql('SELECT setval(\'tabel_user_id_seq\', (SELECT MAX(id) FROM tabel_user))');
        $this->addSql('ALTER TABLE tabel_user ALTER id SET DEFAULT nextval(\'tabel_user_id_seq\')');
        $this->addSql('ALTER TABLE tabel_user ALTER tab_num DROP DEFAULT');
        $this->addSql('ALTER TABLE tabel_user ALTER gr_kod DROP DEFAULT');
        $this->addSql('ALTER INDEX uniq_75d28299e7927c74 RENAME TO uniq_8d93d649e7927c74');
        $this->addSql('CREATE SEQUENCE project_id_seq');
        $this->addSql('SELECT setval(\'project_id_seq\', (SELECT MAX(id) FROM project))');
        $this->addSql('ALTER TABLE project ALTER id SET DEFAULT nextval(\'project_id_seq\')');
        $this->addSql('ALTER TABLE project ALTER deleted SET DEFAULT false');
    }
}
