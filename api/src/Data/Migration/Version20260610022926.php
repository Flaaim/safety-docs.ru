<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260610022926 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE distribution_projects (id VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_contacts (id VARCHAR(36) NOT NULL, project_id VARCHAR(255) NOT NULL, is_unsubscribed TINYINT(1) DEFAULT 0 NOT NULL, email VARCHAR(255) NOT NULL, INDEX IDX_E0E9EA8166D1F9C (project_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_contacts ADD CONSTRAINT FK_E0E9EA8166D1F9C FOREIGN KEY (project_id) REFERENCES distribution_projects (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_contacts DROP FOREIGN KEY FK_E0E9EA8166D1F9C');
        $this->addSql('DROP TABLE distribution_projects');
        $this->addSql('DROP TABLE project_contacts');
    }
}
