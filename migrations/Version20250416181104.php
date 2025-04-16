<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416181104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE torn_attack (id SERIAL NOT NULL, attacker_id INT NOT NULL, defender_id INT NOT NULL, date_time_started TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, date_time_ended TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, result VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3D3B837C65F8CAE3 ON torn_attack (attacker_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_3D3B837C4A3E3B6F ON torn_attack (defender_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE torn_attack ADD CONSTRAINT FK_3D3B837C65F8CAE3 FOREIGN KEY (attacker_id) REFERENCES torn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE torn_attack ADD CONSTRAINT FK_3D3B837C4A3E3B6F FOREIGN KEY (defender_id) REFERENCES torn_user (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE torn_attack DROP CONSTRAINT FK_3D3B837C65F8CAE3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE torn_attack DROP CONSTRAINT FK_3D3B837C4A3E3B6F
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE torn_attack
        SQL);
    }
}
