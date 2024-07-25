<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240315135805 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE games (id UUID NOT NULL, white_player JSON NOT NULL, black_player JSON NOT NULL, state INT NOT NULL, started_on TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ended_on TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN games.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN games.white_player IS \'(DC2Type:json)\'');
        $this->addSql('COMMENT ON COLUMN games.black_player IS \'(DC2Type:json)\'');
        $this->addSql('COMMENT ON COLUMN games.started_on IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN games.ended_on IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA IF NOT EXISTS public');
        $this->addSql('DROP TABLE games');
    }
}
