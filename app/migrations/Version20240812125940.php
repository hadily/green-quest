<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240812125940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE complaints (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, related_to_id INT DEFAULT NULL, admin_id INT DEFAULT NULL, subject VARCHAR(255) NOT NULL, details VARCHAR(255) NOT NULL, status JSON NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_A05AAF3A7E3C61F9 (owner_id), INDEX IDX_A05AAF3A40B4AC4E (related_to_id), INDEX IDX_A05AAF3A642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE complaints ADD CONSTRAINT FK_A05AAF3A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE complaints ADD CONSTRAINT FK_A05AAF3A40B4AC4E FOREIGN KEY (related_to_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE complaints ADD CONSTRAINT FK_A05AAF3A642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE complaints DROP FOREIGN KEY FK_A05AAF3A7E3C61F9');
        $this->addSql('ALTER TABLE complaints DROP FOREIGN KEY FK_A05AAF3A40B4AC4E');
        $this->addSql('ALTER TABLE complaints DROP FOREIGN KEY FK_A05AAF3A642B8210');
        $this->addSql('DROP TABLE complaints');
    }
}
