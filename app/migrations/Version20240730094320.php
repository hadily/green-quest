<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240730094320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649642B8210');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649873649CA');
        $this->addSql('DROP INDEX IDX_8D93D649873649CA ON user');
        $this->addSql('DROP INDEX IDX_8D93D649642B8210 ON user');
        $this->addSql('ALTER TABLE user ADD email VARCHAR(180) NOT NULL, ADD phone_number VARCHAR(255) NOT NULL, DROP managed_by_id, DROP admin_id, DROP name, DROP phone_nuumber, CHANGE last_name last_name VARCHAR(255) NOT NULL, CHANGE discriminator first_name VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_EMAIL ON user');
        $this->addSql('ALTER TABLE user ADD managed_by_id INT DEFAULT NULL, ADD admin_id INT NOT NULL, ADD name VARCHAR(255) DEFAULT NULL, ADD phone_nuumber VARCHAR(255) DEFAULT NULL, ADD discriminator VARCHAR(255) NOT NULL, DROP email, DROP first_name, DROP phone_number, CHANGE last_name last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649873649CA FOREIGN KEY (managed_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_8D93D649873649CA ON user (managed_by_id)');
        $this->addSql('CREATE INDEX IDX_8D93D649642B8210 ON user (admin_id)');
    }
}
