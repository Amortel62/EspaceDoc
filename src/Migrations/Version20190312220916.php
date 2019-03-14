<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190312220916 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE departement CHANGE mail_contact mail_contact VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE fichier CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE filiere CHANGE departement_id departement_id INT DEFAULT NULL, CHANGE typediplome_id typediplome_id INT DEFAULT NULL, CHANGE libelle libelle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE telechargement CHANGE user_id user_id INT DEFAULT NULL, CHANGE fichier_id fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme CHANGE filiere_id filiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE filiere_id filiere_id INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL, CHANGE datenaissance datenaissance DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE departement CHANGE mail_contact mail_contact VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE fichier CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE filiere CHANGE departement_id departement_id INT DEFAULT NULL, CHANGE typediplome_id typediplome_id INT DEFAULT NULL, CHANGE libelle libelle VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE telechargement CHANGE user_id user_id INT DEFAULT NULL, CHANGE fichier_id fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme CHANGE filiere_id filiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE filiere_id filiere_id INT DEFAULT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE nom nom VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE prenom prenom VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE datenaissance datenaissance DATETIME DEFAULT \'NULL\'');
    }
}
