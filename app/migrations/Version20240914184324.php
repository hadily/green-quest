<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240914184324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556BACE54E');
        $this->addSql('DROP INDEX IDX_42C849556BACE54E ON reservation');
        $this->addSql('ALTER TABLE reservation DROP confirmation_id, DROP confirm_reservation');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD confirmation_id INT NOT NULL, ADD confirm_reservation TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556BACE54E FOREIGN KEY (confirmation_id) REFERENCES client (id)');
        $this->addSql('CREATE INDEX IDX_42C849556BACE54E ON reservation (confirmation_id)');
    }
}
