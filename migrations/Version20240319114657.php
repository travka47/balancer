<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240319114657 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE process (id INT UNSIGNED AUTO_INCREMENT NOT NULL, workstation_id INT UNSIGNED NOT NULL, required_ram INT UNSIGNED NOT NULL, required_cpu INT UNSIGNED NOT NULL, INDEX IDX_861D1896E29BB7D (workstation_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workstation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, total_ram INT UNSIGNED NOT NULL, total_cpu INT UNSIGNED NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE workstation_resource (workstation_id INT UNSIGNED NOT NULL, free_ram INT UNSIGNED NOT NULL, free_cpu INT UNSIGNED NOT NULL, PRIMARY KEY(workstation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE process ADD CONSTRAINT FK_861D1896E29BB7D FOREIGN KEY (workstation_id) REFERENCES workstation (id)');
        $this->addSql('ALTER TABLE workstation_resource ADD CONSTRAINT FK_DF23B31EE29BB7D FOREIGN KEY (workstation_id) REFERENCES workstation (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE process DROP FOREIGN KEY FK_861D1896E29BB7D');
        $this->addSql('ALTER TABLE workstation_resource DROP FOREIGN KEY FK_DF23B31EE29BB7D');
        $this->addSql('DROP TABLE process');
        $this->addSql('DROP TABLE workstation');
        $this->addSql('DROP TABLE workstation_resource');
    }
}
