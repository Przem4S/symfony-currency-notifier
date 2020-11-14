<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201114233034 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create member table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE member
                (
                    id         INT AUTO_INCREMENT NOT NULL,
                    email      VARCHAR(255)       NOT NULL,
                    firstname  VARCHAR(100)       NOT NULL,
                    lastname   VARCHAR(100)       NOT NULL,
                    phone      VARCHAR(9)         NOT NULL,
                    birthdate  DATE               NOT NULL,
                    active     SMALLINT           NOT NULL,
                    token      VARCHAR(64)        NOT NULL,
                    created_at DATETIME           NOT NULL,
                    updated_at DATETIME DEFAULT NULL,
                    PRIMARY KEY (id)
                ) DEFAULT CHARACTER SET utf8mb4
                  COLLATE `utf8mb4_unicode_ci`
                  ENGINE = InnoDB'
        );
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE member');
    }
}
