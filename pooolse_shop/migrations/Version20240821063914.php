<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240821063914 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE brand (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE discount (id INT AUTO_INCREMENT NOT NULL, brand_id INT DEFAULT NULL, product_id INT DEFAULT NULL, free_item_treshold SMALLINT DEFAULT NULL, percentage DOUBLE PRECISION DEFAULT NULL, whole INT DEFAULT NULL, UNIQUE INDEX UNIQ_E1E0B40E44F5D008 (brand_id), UNIQUE INDEX UNIQ_E1E0B40E4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, brand_id INT NOT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, png_path VARCHAR(255) NOT NULL, img_path VARCHAR(255) NOT NULL, product_id INT NOT NULL, INDEX IDX_D34A04AD44F5D008 (brand_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE discount ADD CONSTRAINT FK_E1E0B40E44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
        $this->addSql('ALTER TABLE discount ADD CONSTRAINT FK_E1E0B40E4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04AD44F5D008 FOREIGN KEY (brand_id) REFERENCES brand (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discount DROP FOREIGN KEY FK_E1E0B40E44F5D008');
        $this->addSql('ALTER TABLE discount DROP FOREIGN KEY FK_E1E0B40E4584665A');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04AD44F5D008');
        $this->addSql('DROP TABLE brand');
        $this->addSql('DROP TABLE discount');
        $this->addSql('DROP TABLE product');
    }
}
