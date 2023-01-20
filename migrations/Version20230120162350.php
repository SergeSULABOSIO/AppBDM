<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230120162350 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE police ADD cansharericom TINYINT(1) NOT NULL, ADD cansharelocalcom TINYINT(1) NOT NULL, ADD cansharefrontingcom TINYINT(1) NOT NULL, ADD ricompayableby VARCHAR(255) NOT NULL, ADD localcompayableby VARCHAR(255) NOT NULL, ADD frontingcompayableby VARCHAR(255) NOT NULL, DROP commissionpartageable');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE police ADD commissionpartageable INT NOT NULL, DROP cansharericom, DROP cansharelocalcom, DROP cansharefrontingcom, DROP ricompayableby, DROP localcompayableby, DROP frontingcompayableby');
    }
}
