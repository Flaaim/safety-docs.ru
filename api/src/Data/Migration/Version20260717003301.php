<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260717003301 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE categories (category_id CHAR(36) NOT NULL, parent_id CHAR(36) DEFAULT NULL, direction_id CHAR(36) NOT NULL, title VARCHAR(150) NOT NULL, description VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, slug VARCHAR(125) NOT NULL, INDEX IDX_3AF34668727ACA70 (parent_id), INDEX IDX_3AF34668AF73D997 (direction_id), PRIMARY KEY(category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE directions (id CHAR(36) NOT NULL, title VARCHAR(150) NOT NULL, description VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, slug VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution_contacts_files (id VARCHAR(255) NOT NULL, name VARCHAR(55) NOT NULL, date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', complete TINYINT(1) DEFAULT 0 NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE distribution_projects (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE documents (id CHAR(36) NOT NULL, category_id CHAR(36) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', name VARCHAR(255) NOT NULL, amount CHAR(36) NOT NULL, filename VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, INDEX IDX_A2B0728812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messages (id CHAR(36) NOT NULL, status CHAR(36) NOT NULL, date_received DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', recipient_attachments JSON NOT NULL, recipient_email CHAR(36) NOT NULL, recipient_subject VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE newsletters (newsletter_id VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, template_id VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, project_id VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(newsletter_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE payments (id CHAR(36) NOT NULL, external_id VARCHAR(255) DEFAULT NULL, status CHAR(36) NOT NULL, email CHAR(36) NOT NULL, product_id VARCHAR(255) NOT NULL, price CHAR(36) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', return_token_value VARCHAR(255) DEFAULT NULL, return_token_expired DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_contacts (id VARCHAR(36) NOT NULL, project_id VARCHAR(255) NOT NULL, is_unsubscribed TINYINT(1) DEFAULT 0 NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_E0E9EA8166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668727ACA70 FOREIGN KEY (parent_id) REFERENCES categories (category_id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE categories ADD CONSTRAINT FK_3AF34668AF73D997 FOREIGN KEY (direction_id) REFERENCES directions (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE documents ADD CONSTRAINT FK_A2B0728812469DE2 FOREIGN KEY (category_id) REFERENCES categories (category_id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE project_contacts ADD CONSTRAINT FK_E0E9EA8166D1F9C FOREIGN KEY (project_id) REFERENCES distribution_projects (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668727ACA70');
        $this->addSql('ALTER TABLE categories DROP FOREIGN KEY FK_3AF34668AF73D997');
        $this->addSql('ALTER TABLE documents DROP FOREIGN KEY FK_A2B0728812469DE2');
        $this->addSql('ALTER TABLE project_contacts DROP FOREIGN KEY FK_E0E9EA8166D1F9C');
        $this->addSql('DROP TABLE categories');
        $this->addSql('DROP TABLE directions');
        $this->addSql('DROP TABLE distribution_contacts_files');
        $this->addSql('DROP TABLE distribution_projects');
        $this->addSql('DROP TABLE documents');
        $this->addSql('DROP TABLE messages');
        $this->addSql('DROP TABLE newsletters');
        $this->addSql('DROP TABLE payments');
        $this->addSql('DROP TABLE project_contacts');
    }
}
