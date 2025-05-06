<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250422193038 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE photo ADD COLUMN is_background BOOLEAN NOT NULL DEFAULT FALSE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TEMPORARY TABLE __temp__photo AS SELECT id, title, category, filename, description, created_at FROM photo
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE photo
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE photo (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, title VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
        $this->addSql(<<<'SQL'
            INSERT INTO photo (id, title, category, filename, description, created_at) SELECT id, title, category, filename, description, created_at FROM __temp__photo
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE __temp__photo
        SQL);
    }
}
