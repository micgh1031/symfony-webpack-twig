<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180517061034 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F023141FA38');
        $this->addSql('DROP INDEX IDX_64BF3F023141FA38 ON coupon');
        $this->addSql('ALTER TABLE coupon CHANGE campaign_id_id campaign_id INT NOT NULL');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F02F639F774 FOREIGN KEY (campaign_id) REFERENCES campaign (id)');
        $this->addSql('CREATE INDEX IDX_64BF3F02F639F774 ON coupon (campaign_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE coupon DROP FOREIGN KEY FK_64BF3F02F639F774');
        $this->addSql('DROP INDEX IDX_64BF3F02F639F774 ON coupon');
        $this->addSql('ALTER TABLE coupon CHANGE campaign_id campaign_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE coupon ADD CONSTRAINT FK_64BF3F023141FA38 FOREIGN KEY (campaign_id_id) REFERENCES campaign (id)');
        $this->addSql('CREATE INDEX IDX_64BF3F023141FA38 ON coupon (campaign_id_id)');
    }
}
