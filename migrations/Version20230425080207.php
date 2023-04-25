<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425080207 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agents ADD date_demande DATE NOT NULL, CHANGE date_debut date_debut DATE NOT NULL, CHANGE date_fin date_fin DATE NOT NULL');
        $this->addSql('ALTER TABLE agents ADD CONSTRAINT FK_9596AB6EF2C56620 FOREIGN KEY (compte_id) REFERENCES comptes (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9596AB6EF2C56620 ON agents (compte_id)');
        $this->addSql('ALTER TABLE comptes ADD date_attribution DATE DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE agents DROP FOREIGN KEY FK_9596AB6EF2C56620');
        $this->addSql('DROP INDEX UNIQ_9596AB6EF2C56620 ON agents');
        $this->addSql('ALTER TABLE agents DROP date_demande, CHANGE date_debut date_debut DATETIME NOT NULL, CHANGE date_fin date_fin DATETIME NOT NULL');
        $this->addSql('ALTER TABLE comptes DROP date_attribution');
    }
}
