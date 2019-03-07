<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190306153944 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE theme_fichier');
        $this->addSql('ALTER TABLE fichier CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE telechargement CHANGE user_id user_id INT DEFAULT NULL, CHANGE fichier_id fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme CHANGE filiere_id filiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE filiere_id filiere_id INT DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL, CHANGE datenaissance datenaissance DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE theme_fichier (theme_id INT NOT NULL, fichier_id INT NOT NULL, INDEX IDX_C215503CF915CFE (fichier_id), INDEX IDX_C215503C59027487 (theme_id), PRIMARY KEY(theme_id, fichier_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE theme_fichier ADD CONSTRAINT FK_C215503C59027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE theme_fichier ADD CONSTRAINT FK_C215503CF915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fichier CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE telechargement CHANGE user_id user_id INT DEFAULT NULL, CHANGE fichier_id fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE theme CHANGE filiere_id filiere_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE filiere_id filiere_id INT DEFAULT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE nom nom VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE prenom prenom VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE datenaissance datenaissance DATETIME DEFAULT \'NULL\'');
    }
}
