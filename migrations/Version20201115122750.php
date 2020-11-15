<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201115122750 extends AbstractMigration
{
    public function getDescription() : string
    {
        return 'Create unique email in members and add min/max values to subscription';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE UNIQUE INDEX UNIQ_70E4FA78E7927C74 ON member (email)');
        $this->addSql('ALTER TABLE subscription ADD min DOUBLE PRECISION NOT NULL AFTER active, ADD max DOUBLE PRECISION NOT NULL AFTER min');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_70E4FA78E7927C74 ON member');
        $this->addSql('ALTER TABLE subscription DROP min, DROP max');
    }
}
