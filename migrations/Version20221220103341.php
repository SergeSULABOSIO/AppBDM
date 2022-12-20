<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221220103341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE assureur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, siteweb VARCHAR(255) DEFAULT NULL, rccm VARCHAR(255) DEFAULT NULL, idnat VARCHAR(255) DEFAULT NULL, licence VARCHAR(255) DEFAULT NULL, numimpot VARCHAR(255) DEFAULT NULL, isreassureur TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE automobile (id INT AUTO_INCREMENT NOT NULL, model VARCHAR(255) NOT NULL, marque VARCHAR(255) NOT NULL, annee VARCHAR(255) NOT NULL, puissance VARCHAR(255) NOT NULL, valeur NUMERIC(10, 2) DEFAULT NULL, nbsieges INT NOT NULL, utilite VARCHAR(255) NOT NULL, nature INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, siteweb VARCHAR(255) DEFAULT NULL, ispersonnemorale TINYINT(1) NOT NULL, rccm VARCHAR(255) DEFAULT NULL, idnat VARCHAR(255) DEFAULT NULL, numipot VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, poste VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entreprise (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, telephone VARCHAR(255) DEFAULT NULL, rccm VARCHAR(255) DEFAULT NULL, idnat VARCHAR(255) DEFAULT NULL, numimpot VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monnaie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, tauxusd NUMERIC(10, 2) NOT NULL, islocale TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement_commission (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, montant NUMERIC(10, 2) NOT NULL, refnotededebit VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement_partenaire (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, montant NUMERIC(10, 2) NOT NULL, refnotededebit VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement_taxe (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, montant NUMERIC(10, 2) NOT NULL, exercice VARCHAR(255) DEFAULT NULL, refnotededebit VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partenaire (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, part NUMERIC(10, 2) NOT NULL, adresse VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, siteweb VARCHAR(255) DEFAULT NULL, rccm VARCHAR(255) DEFAULT NULL, idnat VARCHAR(255) DEFAULT NULL, numimpot VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE police (id INT AUTO_INCREMENT NOT NULL, dateoperation DATE NOT NULL, dateemission DATE DEFAULT NULL, dateeffet DATE DEFAULT NULL, dateexpiration DATE NOT NULL, reference VARCHAR(255) NOT NULL, idavenant INT NOT NULL, typeavenant VARCHAR(255) NOT NULL, capital NUMERIC(10, 2) NOT NULL, primenette NUMERIC(10, 2) NOT NULL, fronting NUMERIC(10, 2) NOT NULL, arca NUMERIC(10, 2) NOT NULL, tva NUMERIC(10, 2) NOT NULL, fraisadmin NUMERIC(10, 2) NOT NULL, primetotale NUMERIC(10, 2) NOT NULL, discount NUMERIC(10, 2) NOT NULL, modepaiement VARCHAR(255) NOT NULL, ricom NUMERIC(10, 2) NOT NULL, localcom NUMERIC(10, 2) NOT NULL, frontingcom NUMERIC(10, 2) NOT NULL, remarques VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE produit (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, tauxarca NUMERIC(10, 2) NOT NULL, isobligatoire TINYINT(1) NOT NULL, isabonnement TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE secteur (id INT AUTO_INCREMENT NOT NULL, entreprise_id INT NOT NULL, nom VARCHAR(255) NOT NULL, INDEX IDX_8045251FA4AEAFEA (entreprise_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE taxe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, taux NUMERIC(10, 2) NOT NULL, organisation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE secteur ADD CONSTRAINT FK_8045251FA4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE secteur DROP FOREIGN KEY FK_8045251FA4AEAFEA');
        $this->addSql('DROP TABLE assureur');
        $this->addSql('DROP TABLE automobile');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE monnaie');
        $this->addSql('DROP TABLE paiement_commission');
        $this->addSql('DROP TABLE paiement_partenaire');
        $this->addSql('DROP TABLE paiement_taxe');
        $this->addSql('DROP TABLE partenaire');
        $this->addSql('DROP TABLE police');
        $this->addSql('DROP TABLE produit');
        $this->addSql('DROP TABLE secteur');
        $this->addSql('DROP TABLE taxe');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
