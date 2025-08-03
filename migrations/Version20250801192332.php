<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250801192332 extends AbstractMigration
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
        $this->addSql('CREATE TABLE "company" (id SERIAL NOT NULL, created_by_id INT NOT NULL, updated_by_id INT DEFAULT NULL, uuid UUID NOT NULL, email VARCHAR(255) NOT NULL, long_name VARCHAR(255) NOT NULL, short_name VARCHAR(255) NOT NULL, tax_number VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postal_code VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, building_number VARCHAR(255) NOT NULL, apartment_number VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_active BOOLEAN NOT NULL, is_deleted BOOLEAN NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4FBF094FB03A8386 ON "company" (created_by_id)');
        $this->addSql('CREATE INDEX IDX_4FBF094F896DBBDE ON "company" (updated_by_id)');
        $this->addSql('COMMENT ON COLUMN "company".uuid IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN "company".created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "company".updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN "company".deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE "company" ADD CONSTRAINT FK_4FBF094FB03A8386 FOREIGN KEY (created_by_id) REFERENCES "admin" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "company" ADD CONSTRAINT FK_4FBF094F896DBBDE FOREIGN KEY (updated_by_id) REFERENCES "admin" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "company" DROP CONSTRAINT FK_4FBF094FB03A8386');
        $this->addSql('ALTER TABLE "company" DROP CONSTRAINT FK_4FBF094F896DBBDE');
        $this->addSql('DROP TABLE "admin"');
        $this->addSql('DROP TABLE "company"');
    }
}
