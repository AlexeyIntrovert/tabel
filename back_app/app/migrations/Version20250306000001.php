<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250306000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create calendar_days table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE calendar_days (
            id SERIAL PRIMARY KEY,
            date DATE NOT NULL,
            type VARCHAR(1) NOT NULL,
            hours INTEGER NOT NULL,
            CONSTRAINT calendar_days_date_unique UNIQUE (date)
        )');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE calendar_days');
    }
}