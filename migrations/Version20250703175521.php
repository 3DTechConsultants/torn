<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250703175521 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE mug (id SERIAL NOT NULL, defender_id INT NOT NULL, torn_mug_id VARCHAR(255) NOT NULL, date_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, money_mugged INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3ED0F8294A3E3B6F ON mug (defender_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mug ADD CONSTRAINT FK_3ED0F8294A3E3B6F FOREIGN KEY (defender_id) REFERENCES torn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE mug DROP CONSTRAINT FK_3ED0F8294A3E3B6F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE mug
        SQL);
    }
}
