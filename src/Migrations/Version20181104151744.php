<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181104151744 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE game_collections (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(25) NOT NULL, INDEX IDX_D7091ECA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_collections_games (game_collection_id INT NOT NULL, game_id INT NOT NULL, INDEX IDX_9B1C213BFBADCA96 (game_collection_id), INDEX IDX_9B1C213BE48FD905 (game_id), PRIMARY KEY(game_collection_id, game_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game_collections ADD CONSTRAINT FK_D7091ECA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE game_collections_games ADD CONSTRAINT FK_9B1C213BFBADCA96 FOREIGN KEY (game_collection_id) REFERENCES game_collections (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_collections_games ADD CONSTRAINT FK_9B1C213BE48FD905 FOREIGN KEY (game_id) REFERENCES games (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE games CHANGE first_release_date first_release_date DATETIME DEFAULT NULL, CHANGE cover cover VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT NULL, CHANGE confirmation_token_requested_at confirmation_token_requested_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetimetz_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE game_collections_games DROP FOREIGN KEY FK_9B1C213BFBADCA96');
        $this->addSql('DROP TABLE game_collections');
        $this->addSql('DROP TABLE game_collections_games');
        $this->addSql('ALTER TABLE games CHANGE first_release_date first_release_date DATETIME DEFAULT \'NULL\', CHANGE cover cover VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE users CHANGE confirmation_token confirmation_token VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE confirmation_token_requested_at confirmation_token_requested_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetimetz_immutable)\'');
    }
}
