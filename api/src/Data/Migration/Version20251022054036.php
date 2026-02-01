<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251022054036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payments (id CHAR(36) NOT NULL, external_id VARCHAR(255) DEFAULT NULL, status CHAR(36) NOT NULL, email CHAR(36) NOT NULL, product_id VARCHAR(255) NOT NULL, price CHAR(36) NOT NULL, created_at DATETIME NOT NULL, is_send TINYINT(1) NOT NULL, return_token_value VARCHAR(255) DEFAULT NULL, return_token_expired DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE products (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, cipher VARCHAR(25) NOT NULL, price CHAR(36) NOT NULL, file CHAR(36) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE products');
    }
}
