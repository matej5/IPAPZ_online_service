<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191006182344 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE payment (id INT AUTO_INCREMENT NOT NULL, paypal TINYINT(1) NOT NULL, pouzece TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_category (service_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_FF3A42FCED5CA9E6 (service_id), INDEX IDX_FF3A42FC12469DE2 (category_id), PRIMARY KEY(service_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, worker_id INT DEFAULT NULL, firm_name VARCHAR(255) NOT NULL, INDEX IDX_FBD8E0F8A76ED395 (user_id), INDEX IDX_FBD8E0F86B20BA36 (worker_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_category ADD CONSTRAINT FK_FF3A42FCED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_category ADD CONSTRAINT FK_FF3A42FC12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F86B20BA36 FOREIGN KEY (worker_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE category_service');
        $this->addSql('DROP TABLE receipt_service');
        $this->addSql('ALTER TABLE receipt ADD service_id INT DEFAULT NULL, ADD method VARCHAR(255) NOT NULL, ADD activity TINYINT(1) NOT NULL, CHANGE buyer_id buyer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B645ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('CREATE INDEX IDX_5399B645ED5CA9E6 ON receipt (service_id)');
        $this->addSql('ALTER TABLE office ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE office ADD CONSTRAINT FK_74516B027E3C61F9 FOREIGN KEY (owner_id) REFERENCES worker (id)');
        $this->addSql('CREATE INDEX IDX_74516B027E3C61F9 ON office (owner_id)');
        $this->addSql('ALTER TABLE service CHANGE category catalog VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE category_service (category_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_2645DAACED5CA9E6 (service_id), INDEX IDX_2645DAAC12469DE2 (category_id), PRIMARY KEY(category_id, service_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE receipt_service (receipt_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_F2D73F2CED5CA9E6 (service_id), INDEX IDX_F2D73F2C2B5CA896 (receipt_id), PRIMARY KEY(receipt_id, service_id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE category_service ADD CONSTRAINT FK_2645DAAC12469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category_service ADD CONSTRAINT FK_2645DAACED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE receipt_service ADD CONSTRAINT FK_F2D73F2C2B5CA896 FOREIGN KEY (receipt_id) REFERENCES receipt (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE receipt_service ADD CONSTRAINT FK_F2D73F2CED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE service_category');
        $this->addSql('DROP TABLE job');
        $this->addSql('ALTER TABLE office DROP FOREIGN KEY FK_74516B027E3C61F9');
        $this->addSql('DROP INDEX IDX_74516B027E3C61F9 ON office');
        $this->addSql('ALTER TABLE office DROP owner_id');
        $this->addSql('ALTER TABLE receipt DROP FOREIGN KEY FK_5399B645ED5CA9E6');
        $this->addSql('DROP INDEX IDX_5399B645ED5CA9E6 ON receipt');
        $this->addSql('ALTER TABLE receipt DROP service_id, DROP method, DROP activity, CHANGE buyer_id buyer_id INT NOT NULL');
        $this->addSql('ALTER TABLE service CHANGE catalog category VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
