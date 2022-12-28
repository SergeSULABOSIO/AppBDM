<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221228102628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement_commission ADD monnaie_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement_commission ADD CONSTRAINT FK_8AAA6FA898D3FE22 FOREIGN KEY (monnaie_id) REFERENCES monnaie (id)');
        $this->addSql('CREATE INDEX IDX_8AAA6FA898D3FE22 ON paiement_commission (monnaie_id)');
        $this->addSql('ALTER TABLE paiement_partenaire ADD monnaie_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement_partenaire ADD CONSTRAINT FK_A430CD8398D3FE22 FOREIGN KEY (monnaie_id) REFERENCES monnaie (id)');
        $this->addSql('CREATE INDEX IDX_A430CD8398D3FE22 ON paiement_partenaire (monnaie_id)');
        $this->addSql('ALTER TABLE paiement_taxe ADD monnaie_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement_taxe ADD CONSTRAINT FK_A908654498D3FE22 FOREIGN KEY (monnaie_id) REFERENCES monnaie (id)');
        $this->addSql('CREATE INDEX IDX_A908654498D3FE22 ON paiement_taxe (monnaie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement_commission DROP FOREIGN KEY FK_8AAA6FA898D3FE22');
        $this->addSql('DROP INDEX IDX_8AAA6FA898D3FE22 ON paiement_commission');
        $this->addSql('ALTER TABLE paiement_commission DROP monnaie_id');
        $this->addSql('ALTER TABLE paiement_partenaire DROP FOREIGN KEY FK_A430CD8398D3FE22');
        $this->addSql('DROP INDEX IDX_A430CD8398D3FE22 ON paiement_partenaire');
        $this->addSql('ALTER TABLE paiement_partenaire DROP monnaie_id');
        $this->addSql('ALTER TABLE paiement_taxe DROP FOREIGN KEY FK_A908654498D3FE22');
        $this->addSql('DROP INDEX IDX_A908654498D3FE22 ON paiement_taxe');
        $this->addSql('ALTER TABLE paiement_taxe DROP monnaie_id');
    }
}
