<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190218221420 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE fichier (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date DATETIME NOT NULL, extension VARCHAR(255) NOT NULL, taille DOUBLE PRECISION NOT NULL, nom_original VARCHAR(255) NOT NULL, INDEX IDX_9B76551FA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fichier_theme (fichier_id INT NOT NULL, theme_id INT NOT NULL, INDEX IDX_A02C5330F915CFE (fichier_id), INDEX IDX_A02C533059027487 (theme_id), PRIMARY KEY(fichier_id, theme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE telechargement (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, fichier_id INT DEFAULT NULL, nb INT NOT NULL, INDEX IDX_E8C7D809A76ED395 (user_id), INDEX IDX_E8C7D809F915CFE (fichier_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, libelle VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, datenaissance DATETIME DEFAULT NULL, dateinscription DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, datenaissance DATETIME DEFAULT NULL, dateinscription DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fichier ADD CONSTRAINT FK_9B76551FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE fichier_theme ADD CONSTRAINT FK_A02C5330F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichier_theme ADD CONSTRAINT FK_A02C533059027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE telechargement ADD CONSTRAINT FK_E8C7D809A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE telechargement ADD CONSTRAINT FK_E8C7D809F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fichier_theme DROP FOREIGN KEY FK_A02C5330F915CFE');
        $this->addSql('ALTER TABLE telechargement DROP FOREIGN KEY FK_E8C7D809F915CFE');
        $this->addSql('ALTER TABLE fichier_theme DROP FOREIGN KEY FK_A02C533059027487');
        $this->addSql('ALTER TABLE fichier DROP FOREIGN KEY FK_9B76551FA76ED395');
        $this->addSql('ALTER TABLE telechargement DROP FOREIGN KEY FK_E8C7D809A76ED395');
        $this->addSql('DROP TABLE fichier');
        $this->addSql('DROP TABLE fichier_theme');
        $this->addSql('DROP TABLE telechargement');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE utilisateur');
    }
}
