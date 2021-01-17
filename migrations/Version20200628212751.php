<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20200628212751 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('sqlite' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE alias (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, real_email VARCHAR(255) NOT NULL, alias_email VARCHAR(255) NOT NULL, tags CLOB NOT NULL --(DC2Type:array)
        )');
        $this->addSql('insert into alias (id,real_email,alias_email,tags) select id,target,alias,tags from email');
        $this->addSql('DROP TABLE email');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('sqlite' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'sqlite\'.');

        $this->addSql('CREATE TABLE email (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, target VARCHAR(255) NOT NULL, alias VARCHAR(255) NOT NULL, tags CLOB NOT NULL --(DC2Type:array)
        )');
        $this->addSql('insert into email (id,target,alias,tags) select id,real_email,alias_email,tags from alias');
        $this->addSql('DROP TABLE alias');
    }
}
