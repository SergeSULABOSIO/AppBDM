<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221221135451 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement_taxe ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement_taxe ADD CONSTRAINT FK_A9086544A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_A9086544A4AEAFEA ON paiement_taxe (entreprise_id)');
        $this->addSql('ALTER TABLE partenaire ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE partenaire ADD CONSTRAINT FK_32FFA373A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_32FFA373A4AEAFEA ON partenaire (entreprise_id)');
        $this->addSql('ALTER TABLE police ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE police ADD CONSTRAINT FK_E47C5959A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_E47C5959A4AEAFEA ON police (entreprise_id)');
        $this->addSql('ALTER TABLE produit ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_29A5EC27A4AEAFEA ON produit (entreprise_id)');
        $this->addSql('ALTER TABLE taxe ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE taxe ADD CONSTRAINT FK_56322FE9A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_56322FE9A4AEAFEA ON taxe (entreprise_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement_taxe DROP FOREIGN KEY FK_A9086544A4AEAFEA');
        $this->addSql('DROP INDEX IDX_A9086544A4AEAFEA ON paiement_taxe');
        $this->addSql('ALTER TABLE paiement_taxe DROP entreprise_id');
        $this->addSql('ALTER TABLE partenaire DROP FOREIGN KEY FK_32FFA373A4AEAFEA');
        $this->addSql('DROP INDEX IDX_32FFA373A4AEAFEA ON partenaire');
        $this->addSql('ALTER TABLE partenaire DROP entreprise_id');
        $this->addSql('ALTER TABLE police DROP FOREIGN KEY FK_E47C5959A4AEAFEA');
        $this->addSql('DROP INDEX IDX_E47C5959A4AEAFEA ON police');
        $this->addSql('ALTER TABLE police DROP entreprise_id');
        $this->addSql('ALTER TABLE produit DROP FOREIGN KEY FK_29A5EC27A4AEAFEA');
        $this->addSql('DROP INDEX IDX_29A5EC27A4AEAFEA ON produit');
        $this->addSql('ALTER TABLE produit DROP entreprise_id');
        $this->addSql('ALTER TABLE taxe DROP FOREIGN KEY FK_56322FE9A4AEAFEA');
        $this->addSql('DROP INDEX IDX_56322FE9A4AEAFEA ON taxe');
        $this->addSql('ALTER TABLE taxe DROP entreprise_id');
    }
}
