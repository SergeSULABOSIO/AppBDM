<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230101124506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement_taxe ADD taxe_id INT NOT NULL');
        $this->addSql('ALTER TABLE paiement_taxe ADD CONSTRAINT FK_A90865441AB947A4 FOREIGN KEY (taxe_id) REFERENCES taxe (id)');
        $this->addSql('CREATE INDEX IDX_A90865441AB947A4 ON paiement_taxe (taxe_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE paiement_taxe DROP FOREIGN KEY FK_A90865441AB947A4');
        $this->addSql('DROP INDEX IDX_A90865441AB947A4 ON paiement_taxe');
        $this->addSql('ALTER TABLE paiement_taxe DROP taxe_id');
    }
}
