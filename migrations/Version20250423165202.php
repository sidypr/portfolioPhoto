<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423165202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE content (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, identifier VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, content CLOB DEFAULT NULL, image_filename VARCHAR(255) DEFAULT NULL, section VARCHAR(255) DEFAULT NULL, updated_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
            )
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE content
        SQL);
    }
}
