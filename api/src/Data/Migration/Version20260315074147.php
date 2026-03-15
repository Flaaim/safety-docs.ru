<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260315074147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (category_id CHAR(36) NOT NULL, title VARCHAR(150) NOT NULL, description VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, slug VARCHAR(35) NOT NULL, direction_id CHAR(36) NOT NULL, INDEX IDX_3AF34668AF73D997 (direction_id), PRIMARY KEY(category_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668AF73D997 FOREIGN KEY (direction_id) REFERENCES directions (id) ON DELETE RESTRICT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668AF73D997');
        $this->addSql('DROP TABLE categories');
    }
}
