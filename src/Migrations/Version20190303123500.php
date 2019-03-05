<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190303123500 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fichier CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE telechargement CHANGE user_id user_id INT DEFAULT NULL, CHANGE fichier_id fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme ADD filiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme ADD CONSTRAINT FK_9775E708180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('CREATE INDEX IDX_9775E708180AA129 ON theme (filiere_id)');
        $this->addSql('ALTER TABLE user ADD filiere_id INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL, CHANGE datenaissance datenaissance DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649180AA129 FOREIGN KEY (filiere_id) REFERENCES filiere (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649180AA129 ON user (filiere_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fichier CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE telechargement CHANGE user_id user_id INT DEFAULT NULL, CHANGE fichier_id fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme DROP FOREIGN KEY FK_9775E708180AA129');
        $this->addSql('DROP INDEX IDX_9775E708180AA129 ON theme');
        $this->addSql('ALTER TABLE theme DROP filiere_id');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649180AA129');
        $this->addSql('DROP INDEX IDX_8D93D649180AA129 ON user');
        $this->addSql('ALTER TABLE user DROP filiere_id, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE nom nom VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE prenom prenom VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE datenaissance datenaissance DATETIME DEFAULT \'NULL\'');
    }
}
