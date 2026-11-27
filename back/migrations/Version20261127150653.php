<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261127150653 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE oeuvre CHANGE mise_en_avant mise_en_avant TINYINT DEFAULT NULL');
        $this->addSql('UPDATE oeuvre SET mise_en_avant = NULL WHERE mise_en_avant = 0');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_35FE2EFEBFEA0FB9 ON oeuvre (mise_en_avant)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_35FE2EFEBFEA0FB9 ON oeuvre');
        $this->addSql('UPDATE oeuvre SET mise_en_avant = 0 WHERE mise_en_avant IS NULL');
        $this->addSql('ALTER TABLE oeuvre CHANGE mise_en_avant mise_en_avant TINYINT NOT NULL');
    }
}
