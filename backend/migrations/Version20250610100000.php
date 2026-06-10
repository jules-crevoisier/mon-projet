<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250610100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des tables marque et voiture';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE marque (id SERIAL NOT NULL, nom VARCHAR(100) NOT NULL, annee_creation INT NOT NULL, pays_origine VARCHAR(80) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE voiture (id SERIAL NOT NULL, marque_id INT NOT NULL, modele VARCHAR(120) NOT NULL, prix NUMERIC(12, 2) NOT NULL, puissance INT NOT NULL, annee_sortie INT NOT NULL, photo VARCHAR(500) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E9E2810F4827B9B5 ON voiture (marque_id)');
        $this->addSql('ALTER TABLE voiture ADD CONSTRAINT FK_E9E2810F4827B9B5 FOREIGN KEY (marque_id) REFERENCES marque (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE voiture DROP CONSTRAINT FK_E9E2810F4827B9B5');
        $this->addSql('DROP TABLE voiture');
        $this->addSql('DROP TABLE marque');
    }
}
