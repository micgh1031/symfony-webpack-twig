<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180823091209 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE partner ADD website VARCHAR(128) DEFAULT NULL, ADD addressline1 VARCHAR(64) NOT NULL, ADD addressline2 VARCHAR(64) DEFAULT NULL, ADD phone VARCHAR(16) DEFAULT NULL, ADD city VARCHAR(32) NOT NULL, ADD postalcode VARCHAR(8) NOT NULL, ADD latitude VARCHAR(64) NOT NULL, ADD longitude VARCHAR(64) DEFAULT NULL, ADD email VARCHAR(64) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE partner DROP website, DROP addressline1, DROP addressline2, DROP phone, DROP city, DROP postalcode, DROP latitude, DROP longitude, DROP email');
    }
}
