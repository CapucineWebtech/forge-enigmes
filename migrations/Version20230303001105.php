<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230303001105 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE wine_game (id INT AUTO_INCREMENT NOT NULL, wine_game_name VARCHAR(255) NOT NULL, padlock_is_open TINYINT(1) NOT NULL, music INT NOT NULL, temperature DOUBLE PRECISION NOT NULL, bottle_code VARCHAR(4) NOT NULL, user_code_name LONGTEXT NOT NULL, user_code VARCHAR(255) NOT NULL, admin_code VARCHAR(255) NOT NULL, hint LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wine_game_user (wine_game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FF0F46962CA11190 (wine_game_id), INDEX IDX_FF0F4696A76ED395 (user_id), PRIMARY KEY(wine_game_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE wine_game_user ADD CONSTRAINT FK_FF0F46962CA11190 FOREIGN KEY (wine_game_id) REFERENCES wine_game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE wine_game_user ADD CONSTRAINT FK_FF0F4696A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE wine_game_user DROP FOREIGN KEY FK_FF0F46962CA11190');
        $this->addSql('ALTER TABLE wine_game_user DROP FOREIGN KEY FK_FF0F4696A76ED395');
        $this->addSql('DROP TABLE wine_game');
        $this->addSql('DROP TABLE wine_game_user');
    }
}
