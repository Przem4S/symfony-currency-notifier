<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201114234815 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create subscription table';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE subscription
                (
                    id          INT AUTO_INCREMENT NOT NULL,
                    member_id   INT                NOT NULL,
                    currency_id INT                NOT NULL,
                    active      SMALLINT           NOT NULL,
                    created_at  DATETIME           NOT NULL,
                    updated_at  DATETIME DEFAULT NULL,
                    INDEX IDX_A3C664D37597D3FE (member_id),
                    INDEX IDX_A3C664D338248176 (currency_id),
                    PRIMARY KEY (id)
                ) DEFAULT CHARACTER SET utf8mb4
                  COLLATE `utf8mb4_unicode_ci`
                  ENGINE = InnoDB'
        );
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D37597D3FE FOREIGN KEY (member_id) REFERENCES member (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D338248176 FOREIGN KEY (currency_id) REFERENCES currency (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE subscription');
    }
}
