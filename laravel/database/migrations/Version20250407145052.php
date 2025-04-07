<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250407145052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE reactions (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, post_id INT NOT NULL, type VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_38737FB3A76ED395 (user_id), INDEX IDX_38737FB34B89032C (post_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reactions ADD CONSTRAINT FK_38737FB3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reactions ADD CONSTRAINT FK_38737FB34B89032C FOREIGN KEY (post_id) REFERENCES posts (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE posts ADD user_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE posts ADD CONSTRAINT FK_885DBAFAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_885DBAFAA76ED395 ON posts (user_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reactions DROP FOREIGN KEY FK_38737FB3A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reactions DROP FOREIGN KEY FK_38737FB34B89032C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reactions
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE posts DROP FOREIGN KEY FK_885DBAFAA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_885DBAFAA76ED395 ON posts
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE posts DROP user_id
        SQL);
    }
}
