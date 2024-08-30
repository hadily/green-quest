<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240829195913 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7BF396750');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADBF396750');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955ED5CA9E6');
        $this->addSql('ALTER TABLE client_service DROP FOREIGN KEY FK_B3A0DEAF19EB6921');
        $this->addSql('ALTER TABLE client_service DROP FOREIGN KEY FK_B3A0DEAFED5CA9E6');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD27E3C61F9');
        $this->addSql('DROP TABLE client_service');
        $this->addSql('DROP TABLE service');
        $this->addSql('ALTER TABLE event ADD organizer_id INT DEFAULT NULL, ADD name VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, ADD start_date DATE NOT NULL, ADD end_date DATE NOT NULL, ADD category VARCHAR(255) NOT NULL, ADD price INT NOT NULL, ADD nb_participants INT NOT NULL, ADD image_filename VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7876C4DDA FOREIGN KEY (organizer_id) REFERENCES partner (id)');
        $this->addSql('CREATE INDEX IDX_3BAE0AA7876C4DDA ON event (organizer_id)');
        $this->addSql('ALTER TABLE product ADD owner_id INT DEFAULT NULL, ADD name VARCHAR(255) NOT NULL, ADD description VARCHAR(255) NOT NULL, ADD price INT NOT NULL, ADD image_filename VARCHAR(255) NOT NULL, CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD7E3C61F9 FOREIGN KEY (owner_id) REFERENCES partner (id)');
        $this->addSql('CREATE INDEX IDX_D34A04AD7E3C61F9 ON product (owner_id)');
        $this->addSql('DROP INDEX IDX_42C84955ED5CA9E6 ON reservation');
        $this->addSql('ALTER TABLE reservation DROP service_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client_service (client_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_B3A0DEAF19EB6921 (client_id), INDEX IDX_B3A0DEAFED5CA9E6 (service_id), PRIMARY KEY(client_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, service_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, max_participants INT DEFAULT NULL, available TINYINT(1) NOT NULL, promotion TINYINT(1) DEFAULT NULL, image_filename VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, type VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_E19D9AD27E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE client_service ADD CONSTRAINT FK_B3A0DEAF19EB6921 FOREIGN KEY (client_id) REFERENCES client (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE client_service ADD CONSTRAINT FK_B3A0DEAFED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD27E3C61F9 FOREIGN KEY (owner_id) REFERENCES partner (id)');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7876C4DDA');
        $this->addSql('DROP INDEX IDX_3BAE0AA7876C4DDA ON event');
        $this->addSql('ALTER TABLE event DROP organizer_id, DROP name, DROP description, DROP start_date, DROP end_date, DROP category, DROP price, DROP nb_participants, DROP image_filename, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7BF396750 FOREIGN KEY (id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD7E3C61F9');
        $this->addSql('DROP INDEX IDX_D34A04AD7E3C61F9 ON product');
        $this->addSql('ALTER TABLE product DROP owner_id, DROP name, DROP description, DROP price, DROP image_filename, CHANGE id id INT NOT NULL');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADBF396750 FOREIGN KEY (id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reservation ADD service_id INT NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_42C84955ED5CA9E6 ON reservation (service_id)');
    }
}
