<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221221132002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assureur ADD entreprise_id INT NOT NULL');
        $this->addSql('ALTER TABLE assureur ADD CONSTRAINT FK_7B0E5955A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id)');
        $this->addSql('CREATE INDEX IDX_7B0E5955A4AEAFEA ON assureur (entreprise_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE assureur DROP FOREIGN KEY FK_7B0E5955A4AEAFEA');
        $this->addSql('DROP INDEX IDX_7B0E5955A4AEAFEA ON assureur');
        $this->addSql('ALTER TABLE assureur DROP entreprise_id');
    }
}
