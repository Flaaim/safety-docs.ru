<?php

declare(strict_types=1);

namespace App\Data\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251206012936 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answers (id CHAR(36) NOT NULL, text VARCHAR(255) NOT NULL, is_correct TINYINT(1) NOT NULL, img VARCHAR(255) NOT NULL, question_id VARCHAR(36) NOT NULL, INDEX IDX_50D0C6061E27F6BF (question_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE courses (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, part_id CHAR(36) DEFAULT NULL, UNIQUE INDEX UNIQ_A9A55A4C4CE34BEC (part_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE parts (id CHAR(36) NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE questions (id VARCHAR(36) NOT NULL, number VARCHAR(255) NOT NULL, text VARCHAR(255) NOT NULL, question_main_img VARCHAR(255) NOT NULL, ticket_id CHAR(36) NOT NULL, INDEX IDX_8ADC54D5700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tickets (id CHAR(36) NOT NULL, cipher VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, status VARCHAR(255) NOT NULL, updated_at DATETIME DEFAULT NULL, course_id CHAR(36) DEFAULT NULL, UNIQUE INDEX UNIQ_54469DF4591CC992 (course_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE answers ADD CONSTRAINT FK_50D0C6061E27F6BF FOREIGN KEY (question_id) REFERENCES questions (id)');
        $this->addSql('ALTER TABLE courses ADD CONSTRAINT FK_A9A55A4C4CE34BEC FOREIGN KEY (part_id) REFERENCES parts (id)');
        $this->addSql('ALTER TABLE questions ADD CONSTRAINT FK_8ADC54D5700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id)');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4591CC992 FOREIGN KEY (course_id) REFERENCES courses (id)');
        $this->addSql('DROP TABLE chats');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chats (id CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, chat_id BIGINT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_0900_ai_ci`, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_2D68180F1A9A7125 (chat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE answers DROP FOREIGN KEY FK_50D0C6061E27F6BF');
        $this->addSql('ALTER TABLE courses DROP FOREIGN KEY FK_A9A55A4C4CE34BEC');
        $this->addSql('ALTER TABLE questions DROP FOREIGN KEY FK_8ADC54D5700047D2');
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF4591CC992');
        $this->addSql('DROP TABLE answers');
        $this->addSql('DROP TABLE courses');
        $this->addSql('DROP TABLE parts');
        $this->addSql('DROP TABLE questions');
        $this->addSql('DROP TABLE tickets');
    }
}
