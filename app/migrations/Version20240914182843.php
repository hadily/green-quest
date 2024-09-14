<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240914182843 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD event_id INT DEFAULT NULL, ADD product_id INT DEFAULT NULL, ADD client_name VARCHAR(255) NOT NULL, ADD client_phone_number VARCHAR(255) NOT NULL, ADD client_email VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495571F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849554584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('CREATE INDEX IDX_42C8495571F7E88B ON reservation (event_id)');
        $this->addSql('CREATE INDEX IDX_42C849554584665A ON reservation (product_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495571F7E88B');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849554584665A');
        $this->addSql('DROP INDEX IDX_42C8495571F7E88B ON reservation');
        $this->addSql('DROP INDEX IDX_42C849554584665A ON reservation');
        $this->addSql('ALTER TABLE reservation DROP event_id, DROP product_id, DROP client_name, DROP client_phone_number, DROP client_email');
    }
}
