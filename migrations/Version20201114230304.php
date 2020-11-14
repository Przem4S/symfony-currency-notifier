<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201114230304 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create currency table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE currency
            (
                id         INT AUTO_INCREMENT NOT NULL,
                name       VARCHAR(255)       NOT NULL,
                iso        VARCHAR(3)         NOT NULL,
                current    DOUBLE PRECISION   NOT NULL,
                previous   DOUBLE PRECISION DEFAULT NULL,
                created_at DATETIME           NOT NULL,
                updated_at DATETIME         DEFAULT NULL,
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4
              COLLATE `utf8mb4_unicode_ci`
              ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE currency');
    }
}
