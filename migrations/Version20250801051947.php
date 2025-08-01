<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250801051947 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE "admin" (id SERIAL NOT NULL, uuid UUID NOT NULL, email VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_active BOOLEAN NOT NULL, roles JSON NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "admin".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "admin".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "company" (id SERIAL NOT NULL, uuid UUID NOT NULL, long_name VARCHAR(255) NOT NULL, short_name VARCHAR(255) NOT NULL, tax_number VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, building_number VARCHAR(255) NOT NULL, apartment_number VARCHAR(255) DEFAULT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_by VARCHAR(255) NOT NULL, updated_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN "company".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "company".update_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE "admin"');
        $this->addSql('DROP TABLE "company"');
    }
}
