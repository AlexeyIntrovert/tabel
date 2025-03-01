<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250228000000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Rename table user to tabel_user and its sequence';
    }

    public function up(Schema $schema): void
    {
        // Rename the table from user to tabel_user
        $this->addSql('ALTER TABLE "user" RENAME TO tabel_user');
        // Rename the sequence associated with the primary key
        $this->addSql('ALTER SEQUENCE user_id_seq RENAME TO tabel_user_id_seq');
    }

    public function down(Schema $schema): void
    {
        // Revert the sequence name back to user_id_seq
        $this->addSql('ALTER SEQUENCE tabel_user_id_seq RENAME TO user_id_seq');
        // Revert the table name back to user
        $this->addSql('ALTER TABLE tabel_user RENAME TO "user"');
    }
}