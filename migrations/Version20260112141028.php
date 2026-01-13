<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260112141028 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course_plan DROP FOREIGN KEY `FK_15F8867B824D79E7`');
        $this->addSql('DROP INDEX UNIQ_15F8867B824D79E7 ON course_plan');
        $this->addSql('ALTER TABLE course_plan DROP syllabus_id');
        $this->addSql('ALTER TABLE syllabus ADD course_plan_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE syllabus ADD CONSTRAINT FK_4E74AB92E05A0777 FOREIGN KEY (course_plan_id) REFERENCES course_plan (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4E74AB92E05A0777 ON syllabus (course_plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course_plan ADD syllabus_id INT NOT NULL');
        $this->addSql('ALTER TABLE course_plan ADD CONSTRAINT `FK_15F8867B824D79E7` FOREIGN KEY (syllabus_id) REFERENCES syllabus (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_15F8867B824D79E7 ON course_plan (syllabus_id)');
        $this->addSql('ALTER TABLE syllabus DROP FOREIGN KEY FK_4E74AB92E05A0777');
        $this->addSql('DROP INDEX UNIQ_4E74AB92E05A0777 ON syllabus');
        $this->addSql('ALTER TABLE syllabus DROP course_plan_id');
    }
}
