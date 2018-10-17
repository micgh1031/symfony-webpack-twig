<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180823114552 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coupon ADD code VARCHAR(64) DEFAULT NULL');
        $this->addSql('ALTER TABLE partner DROP website, DROP addressline1, DROP addressline2, DROP phone, DROP city, DROP postalcode, DROP latitude, DROP longitude, DROP email');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coupon DROP code');
        $this->addSql('ALTER TABLE partner ADD website VARCHAR(128) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD addressline1 VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, ADD addressline2 VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD phone VARCHAR(16) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD city VARCHAR(32) NOT NULL COLLATE utf8mb4_unicode_ci, ADD postalcode VARCHAR(8) NOT NULL COLLATE utf8mb4_unicode_ci, ADD latitude VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci, ADD longitude VARCHAR(64) DEFAULT NULL COLLATE utf8mb4_unicode_ci, ADD email VARCHAR(64) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
