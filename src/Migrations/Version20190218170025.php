<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190218170025 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fichier CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE telechargement CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL, CHANGE fichier_id fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(255) DEFAULT NULL, ADD prenom VARCHAR(255) DEFAULT NULL, ADD datenaissance DATETIME DEFAULT NULL, ADD dateinscription DATETIME NOT NULL, CHANGE roles roles JSON NOT NULL');
        $this->addSql('ALTER TABLE utilisateur CHANGE datenaissance datenaissance DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fichier CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE telechargement CHANGE utilisateur_id utilisateur_id INT DEFAULT NULL, CHANGE fichier_id fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user DROP nom, DROP prenom, DROP datenaissance, DROP dateinscription, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin');
        $this->addSql('ALTER TABLE utilisateur CHANGE datenaissance datenaissance DATETIME DEFAULT \'NULL\'');
    }
}
