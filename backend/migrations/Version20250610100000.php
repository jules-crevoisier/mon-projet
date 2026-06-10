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
        $this->addSql("INSERT INTO marque (nom, annee_creation, pays_origine) VALUES ('Ferrari', 1947, 'Italie'), ('Porsche', 1931, 'Allemagne'), ('McLaren', 1963, 'Royaume-Uni'), ('Bugatti', 1909, 'France')");
        $this->addSql("INSERT INTO voiture (marque_id, modele, prix, puissance, annee_sortie, photo) VALUES (1, 'Ferrari 296 GTB', 269000.00, 830, 2022, 'https://images.unsplash.com/photo-1592198084033-aade902d1aae?auto=format&fit=crop&w=900&q=80'), (2, 'Porsche 911 GT3 RS', 234977.00, 525, 2023, 'https://images.unsplash.com/photo-1611859266238-4b98091d9d9b?auto=format&fit=crop&w=900&q=80'), (3, 'McLaren Artura', 236500.00, 680, 2021, 'https://images.unsplash.com/photo-1621135802920-133df287f89c?auto=format&fit=crop&w=900&q=80'), (4, 'Bugatti Chiron', 2900000.00, 1500, 2016, 'https://images.unsplash.com/photo-1566024164372-0281f1133aa6?auto=format&fit=crop&w=900&q=80')");
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE voiture DROP CONSTRAINT FK_E9E2810F4827B9B5');
        $this->addSql('DROP TABLE voiture');
        $this->addSql('DROP TABLE marque');
    }
}
