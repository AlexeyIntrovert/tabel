<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240313000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creates timesheet table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE timesheet (
            id SERIAL PRIMARY KEY,
            user_id INTEGER NOT NULL,
            date DATE NOT NULL,
            hours NUMERIC(4,2) NOT NULL,
            project_id INTEGER NOT NULL,
            group_id INTEGER NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        )');

        // Add foreign key constraints
        $this->addSql('ALTER TABLE timesheet 
            ADD CONSTRAINT FK_timesheet_user FOREIGN KEY (user_id) REFERENCES "tabel_user" (id),
            ADD CONSTRAINT FK_timesheet_project FOREIGN KEY (project_id) REFERENCES project (id),
            ADD CONSTRAINT FK_timesheet_group FOREIGN KEY (group_id) REFERENCES "tabel_groups" (id)
        ');

        // Add indexes
        $this->addSql('CREATE INDEX IDX_timesheet_user ON timesheet (user_id)');
        $this->addSql('CREATE INDEX IDX_timesheet_date ON timesheet (date)');
        $this->addSql('CREATE INDEX IDX_timesheet_project ON timesheet (project_id)');
        $this->addSql('CREATE INDEX IDX_timesheet_group ON timesheet (group_id)');

        // Add trigger for updated_at
        $this->addSql('CREATE OR REPLACE FUNCTION update_timesheet_updated_at()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ language plpgsql');

        $this->addSql('CREATE TRIGGER update_timesheet_updated_at
            BEFORE UPDATE ON timesheet
            FOR EACH ROW
            EXECUTE FUNCTION update_timesheet_updated_at()');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TRIGGER IF EXISTS update_timesheet_updated_at ON timesheet');
        $this->addSql('DROP FUNCTION IF EXISTS update_timesheet_updated_at()');
        $this->addSql('DROP TABLE timesheet');
    }
}