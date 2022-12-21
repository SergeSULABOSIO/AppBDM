<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221221134950 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE automobile ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE automobile ADD CONSTRAINT FK_BFCEA087A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_BFCEA087A4AEAFEA ON automobile (entreprise_id)');
        $this->addSql('ALTER TABLE client ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_C7440455A4AEAFEA ON client (entreprise_id)');
        $this->addSql('ALTER TABLE contact ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_4C62E638A4AEAFEA ON contact (entreprise_id)');
        $this->addSql('ALTER TABLE monnaie ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE monnaie ADD CONSTRAINT FK_B3A6E2E6A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_B3A6E2E6A4AEAFEA ON monnaie (entreprise_id)');
        $this->addSql('ALTER TABLE paiement_commission ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement_commission ADD CONSTRAINT FK_8AAA6FA8A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_8AAA6FA8A4AEAFEA ON paiement_commission (entreprise_id)');
        $this->addSql('ALTER TABLE paiement_partenaire ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement_partenaire ADD CONSTRAINT FK_A430CD83A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_A430CD83A4AEAFEA ON paiement_partenaire (entreprise_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE automobile DROP FOREIGN KEY FK_BFCEA087A4AEAFEA');
        $this->addSql('DROP INDEX IDX_BFCEA087A4AEAFEA ON automobile');
        $this->addSql('ALTER TABLE automobile DROP entreprise_id');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455A4AEAFEA');
        $this->addSql('DROP INDEX IDX_C7440455A4AEAFEA ON client');
        $this->addSql('ALTER TABLE client DROP entreprise_id');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638A4AEAFEA');
        $this->addSql('DROP INDEX IDX_4C62E638A4AEAFEA ON contact');
        $this->addSql('ALTER TABLE contact DROP entreprise_id');
        $this->addSql('ALTER TABLE monnaie DROP FOREIGN KEY FK_B3A6E2E6A4AEAFEA');
        $this->addSql('DROP INDEX IDX_B3A6E2E6A4AEAFEA ON monnaie');
        $this->addSql('ALTER TABLE monnaie DROP entreprise_id');
        $this->addSql('ALTER TABLE paiement_commission DROP FOREIGN KEY FK_8AAA6FA8A4AEAFEA');
        $this->addSql('DROP INDEX IDX_8AAA6FA8A4AEAFEA ON paiement_commission');
        $this->addSql('ALTER TABLE paiement_commission DROP entreprise_id');
        $this->addSql('ALTER TABLE paiement_partenaire DROP FOREIGN KEY FK_A430CD83A4AEAFEA');
        $this->addSql('DROP INDEX IDX_A430CD83A4AEAFEA ON paiement_partenaire');
        $this->addSql('ALTER TABLE paiement_partenaire DROP entreprise_id');
    }
}
