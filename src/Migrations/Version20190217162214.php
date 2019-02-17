<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190217162214 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE telechargement ADD utilisateur_id INT DEFAULT NULL, ADD fichier_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE telechargement ADD CONSTRAINT FK_E8C7D809FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE telechargement ADD CONSTRAINT FK_E8C7D809F915CFE FOREIGN KEY (fichier_id) REFERENCES fichier (id)');
        $this->addSql('CREATE INDEX IDX_E8C7D809FB88E14F ON telechargement (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_E8C7D809F915CFE ON telechargement (fichier_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE telechargement DROP FOREIGN KEY FK_E8C7D809FB88E14F');
        $this->addSql('ALTER TABLE telechargement DROP FOREIGN KEY FK_E8C7D809F915CFE');
        $this->addSql('DROP INDEX IDX_E8C7D809FB88E14F ON telechargement');
        $this->addSql('DROP INDEX IDX_E8C7D809F915CFE ON telechargement');
        $this->addSql('ALTER TABLE telechargement DROP utilisateur_id, DROP fichier_id');
    }
}
