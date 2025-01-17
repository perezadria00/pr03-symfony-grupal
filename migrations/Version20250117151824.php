<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250117151824 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nurse ADD speciality VARCHAR(30) DEFAULT NULL, ADD shift VARCHAR(30) DEFAULT NULL, ADD phone VARCHAR(20) DEFAULT NULL, CHANGE password password VARCHAR(20) NOT NULL, CHANGE user username VARCHAR(30) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE nurse DROP speciality, DROP shift, DROP phone, CHANGE password password VARCHAR(60) NOT NULL, CHANGE username user VARCHAR(30) NOT NULL');
    }
}
