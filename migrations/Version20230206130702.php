<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230206130702 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category_prestation (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE prestations ADD category_id INT DEFAULT NULL, CHANGE time time INT NOT NULL');
        $this->addSql('ALTER TABLE prestations ADD CONSTRAINT FK_B338D8D112469DE2 FOREIGN KEY (category_id) REFERENCES category_prestation (id)');
        $this->addSql('CREATE INDEX IDX_B338D8D112469DE2 ON prestations (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prestations DROP FOREIGN KEY FK_B338D8D112469DE2');
        $this->addSql('DROP TABLE category_prestation');
        $this->addSql('DROP INDEX IDX_B338D8D112469DE2 ON prestations');
        $this->addSql('ALTER TABLE prestations DROP category_id, CHANGE time time TIME NOT NULL');
    }
}
