<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230127164456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement_taxe_police DROP FOREIGN KEY FK_F5216C1737E60BE1');
        $this->addSql('ALTER TABLE paiement_taxe_police DROP FOREIGN KEY FK_F5216C17783F755');
        $this->addSql('DROP TABLE paiement_taxe_police');
        $this->addSql('ALTER TABLE paiement_taxe ADD police_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement_taxe ADD CONSTRAINT FK_A908654437E60BE1 FOREIGN KEY (police_id) REFERENCES police (id)');
        $this->addSql('CREATE INDEX IDX_A908654437E60BE1 ON paiement_taxe (police_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE paiement_taxe_police (paiement_taxe_id INT NOT NULL, police_id INT NOT NULL, INDEX IDX_F5216C17783F755 (paiement_taxe_id), INDEX IDX_F5216C1737E60BE1 (police_id), PRIMARY KEY(paiement_taxe_id, police_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE paiement_taxe_police ADD CONSTRAINT FK_F5216C1737E60BE1 FOREIGN KEY (police_id) REFERENCES police (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paiement_taxe_police ADD CONSTRAINT FK_F5216C17783F755 FOREIGN KEY (paiement_taxe_id) REFERENCES paiement_taxe (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE paiement_taxe DROP FOREIGN KEY FK_A908654437E60BE1');
        $this->addSql('DROP INDEX IDX_A908654437E60BE1 ON paiement_taxe');
        $this->addSql('ALTER TABLE paiement_taxe DROP police_id');
    }
}
