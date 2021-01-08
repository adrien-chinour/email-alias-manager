<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210108163504 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE migration_versions');
        $this->addSql('CREATE TEMPORARY TABLE __temp__alias AS SELECT id, real_email, alias_email FROM alias');
        $this->addSql('DROP TABLE alias');
        $this->addSql('CREATE TABLE alias (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, real_email VARCHAR(255) NOT NULL COLLATE BINARY, alias_email VARCHAR(255) NOT NULL COLLATE BINARY)');
        $this->addSql('INSERT INTO alias (id, real_email, alias_email) SELECT id, real_email, alias_email FROM __temp__alias');
        $this->addSql('DROP TABLE __temp__alias');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE migration_versions (version VARCHAR(14) NOT NULL COLLATE BINARY, executed_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , PRIMARY KEY(version))');
        $this->addSql('ALTER TABLE alias ADD COLUMN tags CLOB NOT NULL COLLATE BINARY');
    }
}
