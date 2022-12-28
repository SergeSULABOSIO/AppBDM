<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221228104756 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paiement_commission_police (paiement_commission_id INT NOT NULL, police_id INT NOT NULL, INDEX IDX_28DA4F34654CF4FC (paiement_commission_id), INDEX IDX_28DA4F3437E60BE1 (police_id), PRIMARY KEY(paiement_commission_id, police_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE paiement_partenaire_police (paiement_partenaire_id INT NOT NULL, police_id INT NOT NULL, INDEX IDX_6AEF750BDDBFF9E2 (paiement_partenaire_id), INDEX IDX_6AEF750B37E60BE1 (police_id), PRIMARY KEY(paiement_partenaire_id, police_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE paiement_commission_police ADD CONSTRAINT FK_28DA4F34654CF4FC FOREIGN KEY (paiement_commission_id) REFERENCES paiement_commission (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paiement_commission_police ADD CONSTRAINT FK_28DA4F3437E60BE1 FOREIGN KEY (police_id) REFERENCES police (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paiement_partenaire_police ADD CONSTRAINT FK_6AEF750BDDBFF9E2 FOREIGN KEY (paiement_partenaire_id) REFERENCES paiement_partenaire (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paiement_partenaire_police ADD CONSTRAINT FK_6AEF750B37E60BE1 FOREIGN KEY (police_id) REFERENCES police (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement_commission_police DROP FOREIGN KEY FK_28DA4F34654CF4FC');
        $this->addSql('ALTER TABLE paiement_commission_police DROP FOREIGN KEY FK_28DA4F3437E60BE1');
        $this->addSql('ALTER TABLE paiement_partenaire_police DROP FOREIGN KEY FK_6AEF750BDDBFF9E2');
        $this->addSql('ALTER TABLE paiement_partenaire_police DROP FOREIGN KEY FK_6AEF750B37E60BE1');
        $this->addSql('DROP TABLE paiement_commission_police');
        $this->addSql('DROP TABLE paiement_partenaire_police');
    }
}
