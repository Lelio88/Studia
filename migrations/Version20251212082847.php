<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212082847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE course_plan (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, general_plan LONGTEXT NOT NULL, evalevaluation_criteria JSON NOT NULL, nb_sessions_planned INT NOT NULL, expected_total_hours INT NOT NULL, created_at DATETIME NOT NULL, syllabus_id INT NOT NULL, UNIQUE INDEX UNIQ_15F8867B824D79E7 (syllabus_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE exercise (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, instruction VARCHAR(255) NOT NULL, difficulty VARCHAR(6) NOT NULL, expected_duration_minutes INT NOT NULL, correction JSON DEFAULT NULL, session_id INT NOT NULL, INDEX IDX_AEDAD51C613FECDF (session_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, index_number INT NOT NULL, title VARCHAR(255) NOT NULL, objectives JSON NOT NULL, contents JSON NOT NULL, activities JSON NOT NULL, resources JSON NOT NULL, done TINYINT(1) NOT NULL, actual_notes LONGTEXT DEFAULT NULL, planned_duration_minutes INT NOT NULL, course_plan_id INT NOT NULL, INDEX IDX_D044D5D4E05A0777 (course_plan_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE student_progress (id INT AUTO_INCREMENT NOT NULL, global_level VARCHAR(12) NOT NULL, notes LONGTEXT DEFAULT NULL, student_id INT NOT NULL, course_plan_id INT NOT NULL, INDEX IDX_918ABEDDCB944F1A (student_id), INDEX IDX_918ABEDDE05A0777 (course_plan_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE syllabus (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, raw_text LONGTEXT NOT NULL, extracted_competences JSON DEFAULT NULL, created_at DATETIME NOT NULL, owner_id INT NOT NULL, INDEX IDX_4E74AB927E3C61F9 (owner_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE course_plan ADD CONSTRAINT FK_15F8867B824D79E7 FOREIGN KEY (syllabus_id) REFERENCES syllabus (id)');
        $this->addSql('ALTER TABLE exercise ADD CONSTRAINT FK_AEDAD51C613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4E05A0777 FOREIGN KEY (course_plan_id) REFERENCES course_plan (id)');
        $this->addSql('ALTER TABLE student_progress ADD CONSTRAINT FK_918ABEDDCB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE student_progress ADD CONSTRAINT FK_918ABEDDE05A0777 FOREIGN KEY (course_plan_id) REFERENCES course_plan (id)');
        $this->addSql('ALTER TABLE syllabus ADD CONSTRAINT FK_4E74AB927E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course_plan DROP FOREIGN KEY FK_15F8867B824D79E7');
        $this->addSql('ALTER TABLE exercise DROP FOREIGN KEY FK_AEDAD51C613FECDF');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4E05A0777');
        $this->addSql('ALTER TABLE student_progress DROP FOREIGN KEY FK_918ABEDDCB944F1A');
        $this->addSql('ALTER TABLE student_progress DROP FOREIGN KEY FK_918ABEDDE05A0777');
        $this->addSql('ALTER TABLE syllabus DROP FOREIGN KEY FK_4E74AB927E3C61F9');
        $this->addSql('DROP TABLE course_plan');
        $this->addSql('DROP TABLE exercise');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE student_progress');
        $this->addSql('DROP TABLE syllabus');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
