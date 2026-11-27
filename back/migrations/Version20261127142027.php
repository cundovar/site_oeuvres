<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261127142027 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categorie (id INT AUTO_INCREMENT NOT NULL, nom_fr VARCHAR(150) NOT NULL, nom_en VARCHAR(150) DEFAULT NULL, dans_filtres TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, chemin_stockage VARCHAR(500) NOT NULL, nom_original VARCHAR(255) NOT NULL, mime_type VARCHAR(100) NOT NULL, taille INT NOT NULL, largeur INT DEFAULT NULL, hauteur INT DEFAULT NULL, alt_fr VARCHAR(255) DEFAULT NULL, alt_en VARCHAR(255) DEFAULT NULL, duree INT DEFAULT NULL, etat_traitement VARCHAR(30) DEFAULT NULL, progression_traitement INT DEFAULT NULL, erreur_traitement LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_6A2CA10C78F7C0EB (chemin_stockage), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oeuvre (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(180) NOT NULL, titre_fr VARCHAR(200) NOT NULL, titre_en VARCHAR(200) DEFAULT NULL, presentation_fr LONGTEXT DEFAULT NULL, presentation_en LONGTEXT DEFAULT NULL, annee INT DEFAULT NULL, statut VARCHAR(20) NOT NULL, visible_index TINYINT NOT NULL, mise_en_avant TINYINT NOT NULL, ordre_index INT NOT NULL, ordre_serie INT NOT NULL, procede_fr VARCHAR(255) DEFAULT NULL, procede_en VARCHAR(255) DEFAULT NULL, iterations VARCHAR(255) DEFAULT NULL, tirage_fr VARCHAR(255) DEFAULT NULL, tirage_en VARCHAR(255) DEFAULT NULL, format VARCHAR(255) DEFAULT NULL, edition VARCHAR(255) DEFAULT NULL, prix_sur_demande TINYINT NOT NULL, prix NUMERIC(10, 2) DEFAULT NULL, nombre_total INT DEFAULT NULL, nombre_restant INT DEFAULT NULL, seo_titre_fr VARCHAR(255) DEFAULT NULL, seo_titre_en VARCHAR(255) DEFAULT NULL, seo_description_fr VARCHAR(500) DEFAULT NULL, seo_description_en VARCHAR(500) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, serie_id INT DEFAULT NULL, media_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_35FE2EFE989D9B62 (slug), INDEX IDX_35FE2EFED94388BD (serie_id), INDEX IDX_35FE2EFEEA9FDD75 (media_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE oeuvre_categorie (oeuvre_id INT NOT NULL, categorie_id INT NOT NULL, INDEX IDX_50ED1EBF88194DE8 (oeuvre_id), INDEX IDX_50ED1EBFBCF5E72D (categorie_id), PRIMARY KEY (oeuvre_id, categorie_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE serie (id INT AUTO_INCREMENT NOT NULL, slug VARCHAR(180) NOT NULL, titre_fr VARCHAR(200) NOT NULL, titre_en VARCHAR(200) DEFAULT NULL, introduction_fr LONGTEXT DEFAULT NULL, introduction_en LONGTEXT DEFAULT NULL, statut VARCHAR(20) NOT NULL, visible_index TINYINT NOT NULL, ordre_index INT NOT NULL, procede_fr VARCHAR(255) DEFAULT NULL, procede_en VARCHAR(255) DEFAULT NULL, tirage_fr VARCHAR(255) DEFAULT NULL, tirage_en VARCHAR(255) DEFAULT NULL, exposition_fr VARCHAR(255) DEFAULT NULL, exposition_en VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, piece_maitresse_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_AA3A9334989D9B62 (slug), UNIQUE INDEX UNIQ_AA3A9334BC22666C (piece_maitresse_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE oeuvre ADD CONSTRAINT FK_35FE2EFED94388BD FOREIGN KEY (serie_id) REFERENCES serie (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oeuvre ADD CONSTRAINT FK_35FE2EFEEA9FDD75 FOREIGN KEY (media_id) REFERENCES media (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE oeuvre_categorie ADD CONSTRAINT FK_50ED1EBF88194DE8 FOREIGN KEY (oeuvre_id) REFERENCES oeuvre (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE oeuvre_categorie ADD CONSTRAINT FK_50ED1EBFBCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE serie ADD CONSTRAINT FK_AA3A9334BC22666C FOREIGN KEY (piece_maitresse_id) REFERENCES oeuvre (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE oeuvre DROP FOREIGN KEY FK_35FE2EFED94388BD');
        $this->addSql('ALTER TABLE oeuvre DROP FOREIGN KEY FK_35FE2EFEEA9FDD75');
        $this->addSql('ALTER TABLE oeuvre_categorie DROP FOREIGN KEY FK_50ED1EBF88194DE8');
        $this->addSql('ALTER TABLE oeuvre_categorie DROP FOREIGN KEY FK_50ED1EBFBCF5E72D');
        $this->addSql('ALTER TABLE serie DROP FOREIGN KEY FK_AA3A9334BC22666C');
        $this->addSql('DROP TABLE categorie');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE oeuvre');
        $this->addSql('DROP TABLE oeuvre_categorie');
        $this->addSql('DROP TABLE serie');
    }
}
