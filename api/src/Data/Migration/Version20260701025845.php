<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260701025845 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF346684584665A');
        $this->addSql('DROP INDEX UNIQ_3AF346684584665A ON categories');
        $this->addSql('ALTER TABLE categories DROP product_id');
        $this->addSql('ALTER TABLE directions DROP breadcrumb_slug, DROP breadcrumb_title, CHANGE slug slug VARCHAR(50) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories ADD product_id CHAR(36) DEFAULT NULL');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF346684584665A FOREIGN KEY (product_id) REFERENCES products (id) ON UPDATE NO ACTION ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3AF346684584665A ON categories (product_id)');
        $this->addSql('ALTER TABLE directions ADD breadcrumb_slug VARCHAR(35) DEFAULT NULL, ADD breadcrumb_title VARCHAR(150) DEFAULT NULL, CHANGE slug slug VARCHAR(35) NOT NULL');
    }
}
