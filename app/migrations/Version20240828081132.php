<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240828081132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE admin (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, writer_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, sub_title VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, date DATE NOT NULL, likes INT DEFAULT NULL, image_filename VARCHAR(255) NOT NULL, INDEX IDX_23A0E661BC7E6B6 (writer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT NOT NULL, admin_id INT DEFAULT NULL, localisation VARCHAR(255) DEFAULT NULL, INDEX IDX_C7440455642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client_service (client_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_B3A0DEAF19EB6921 (client_id), INDEX IDX_B3A0DEAFED5CA9E6 (service_id), PRIMARY KEY(client_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE complaints (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, related_to_id INT DEFAULT NULL, admin_id INT DEFAULT NULL, subject VARCHAR(255) NOT NULL, details VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, date DATE NOT NULL, reply VARCHAR(255) DEFAULT NULL, INDEX IDX_A05AAF3A7E3C61F9 (owner_id), INDEX IDX_A05AAF3A40B4AC4E (related_to_id), INDEX IDX_A05AAF3A642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE partner (id INT NOT NULL, admin_id INT NOT NULL, company_name VARCHAR(255) NOT NULL, company_description VARCHAR(255) DEFAULT NULL, localisation VARCHAR(255) DEFAULT NULL, INDEX IDX_312B3E16642B8210 (admin_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, UNIQUE INDEX UNIQ_9BACE7E1C74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, confirmation_id INT NOT NULL, service_id INT NOT NULL, reservation_date DATE NOT NULL, confirm_reservation TINYINT(1) NOT NULL, INDEX IDX_42C849556BACE54E (confirmation_id), INDEX IDX_42C84955ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, service_name VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, max_participants INT DEFAULT NULL, available TINYINT(1) NOT NULL, promotion TINYINT(1) DEFAULT NULL, image_filename VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_E19D9AD27E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE admin ADD CONSTRAINT FK_880E0D76BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E661BC7E6B6 FOREIGN KEY (writer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_service ADD CONSTRAINT FK_B3A0DEAF19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_service ADD CONSTRAINT FK_B3A0DEAFED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE complaints ADD CONSTRAINT FK_A05AAF3A7E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE complaints ADD CONSTRAINT FK_A05AAF3A40B4AC4E FOREIGN KEY (related_to_id) REFERENCES article (id)');
        $this->addSql('ALTER TABLE complaints ADD CONSTRAINT FK_A05AAF3A642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7BF396750 FOREIGN KEY (id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16642B8210 FOREIGN KEY (admin_id) REFERENCES admin (id)');
        $this->addSql('ALTER TABLE partner ADD CONSTRAINT FK_312B3E16BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADBF396750 FOREIGN KEY (id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556BACE54E FOREIGN KEY (confirmation_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD27E3C61F9 FOREIGN KEY (owner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE user ADD image_filename VARCHAR(255) DEFAULT NULL, ADD type VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE admin DROP FOREIGN KEY FK_880E0D76BF396750');
        $this->addSql('ALTER TABLE article DROP FOREIGN KEY FK_23A0E661BC7E6B6');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455642B8210');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455BF396750');
        $this->addSql('ALTER TABLE client_service DROP FOREIGN KEY FK_B3A0DEAF19EB6921');
        $this->addSql('ALTER TABLE client_service DROP FOREIGN KEY FK_B3A0DEAFED5CA9E6');
        $this->addSql('ALTER TABLE complaints DROP FOREIGN KEY FK_A05AAF3A7E3C61F9');
        $this->addSql('ALTER TABLE complaints DROP FOREIGN KEY FK_A05AAF3A40B4AC4E');
        $this->addSql('ALTER TABLE complaints DROP FOREIGN KEY FK_A05AAF3A642B8210');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7BF396750');
        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E16642B8210');
        $this->addSql('ALTER TABLE partner DROP FOREIGN KEY FK_312B3E16BF396750');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADBF396750');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556BACE54E');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955ED5CA9E6');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD27E3C61F9');
        $this->addSql('DROP TABLE admin');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE client_service');
        $this->addSql('DROP TABLE complaints');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE partner');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE refresh_tokens');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE service');
        $this->addSql('ALTER TABLE user DROP image_filename, DROP type');
    }
}
