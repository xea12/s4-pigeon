<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241115190321 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE campaign_open (id INT AUTO_INCREMENT NOT NULL, campaign_id INT NOT NULL, customer_id INT NOT NULL, campaign_sent_id INT NOT NULL, opened_at DATETIME NOT NULL, ip_address VARCHAR(45) DEFAULT NULL, user_agent VARCHAR(255) DEFAULT NULL, INDEX IDX_A597B846F639F774 (campaign_id), INDEX IDX_A597B8469395C3F3 (customer_id), INDEX IDX_A597B846EB30F5C8 (campaign_sent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE campaign_sent (id INT AUTO_INCREMENT NOT NULL, campaign_id INT NOT NULL, customer_id INT NOT NULL, sent_at DATETIME NOT NULL, status VARCHAR(255) DEFAULT NULL, tracking_id VARCHAR(255) DEFAULT NULL, INDEX IDX_BB23114BF639F774 (campaign_id), INDEX IDX_BB23114B9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE import_history (id INT AUTO_INCREMENT NOT NULL, import_date DATETIME NOT NULL, email VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE kampanie (id INT AUTO_INCREMENT NOT NULL, nazwa VARCHAR(255) NOT NULL, temat VARCHAR(255) NOT NULL, nazwa_nadawcy VARCHAR(255) NOT NULL, email_nadawcy VARCHAR(100) NOT NULL, data_utworzenia DATETIME NOT NULL, data_wysylki DATETIME DEFAULT NULL, status VARCHAR(20) NOT NULL, segment VARCHAR(255) DEFAULT NULL, html_template LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE klienci (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(1000) NOT NULL, imie VARCHAR(1000) DEFAULT NULL, zakup VARCHAR(1000) DEFAULT NULL, product_id VARCHAR(1000) DEFAULT NULL, product_nazwa VARCHAR(255) DEFAULT NULL, product_obrazek VARCHAR(255) DEFAULT NULL, product_cena VARCHAR(255) DEFAULT NULL, product_cenalow VARCHAR(255) DEFAULT NULL, product_typ VARCHAR(255) DEFAULT NULL, emhash VARCHAR(255) DEFAULT NULL, rabat VARCHAR(255) DEFAULT NULL, discount VARCHAR(255) DEFAULT NULL, customers_days_from_order VARCHAR(255) DEFAULT NULL, customers_orders_count VARCHAR(255) DEFAULT NULL, customers_balance VARCHAR(255) DEFAULT NULL, customers_firma VARCHAR(255) DEFAULT NULL, customers_value_class VARCHAR(255) DEFAULT NULL, customers_time_class VARCHAR(255) DEFAULT NULL, printer_id VARCHAR(255) DEFAULT NULL, printer_name VARCHAR(255) DEFAULT NULL, printer_image VARCHAR(255) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, last_review VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, subject_type VARCHAR(255) DEFAULT NULL, nazwa_short VARCHAR(255) DEFAULT NULL, liczba VARCHAR(255) DEFAULT NULL, producent VARCHAR(255) DEFAULT NULL, technologia VARCHAR(255) DEFAULT NULL, lokalny VARCHAR(255) DEFAULT NULL, preheader VARCHAR(255) DEFAULT NULL, b2b_b2c VARCHAR(255) DEFAULT NULL, product_rodzaj_nazwa VARCHAR(255) DEFAULT NULL, product_typ_nazwa VARCHAR(255) DEFAULT NULL, odroczony VARCHAR(255) DEFAULT NULL, publiczny VARCHAR(255) DEFAULT NULL, przedszkole VARCHAR(255) DEFAULT NULL, segment_product_type VARCHAR(255) DEFAULT NULL, segment_otwierane VARCHAR(255) DEFAULT NULL, segment_firma VARCHAR(255) DEFAULT NULL, segment_czas VARCHAR(255) DEFAULT NULL, segment VARCHAR(255) DEFAULT NULL, ab_test VARCHAR(255) DEFAULT NULL, add_date DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE campaign_open ADD CONSTRAINT FK_A597B846F639F774 FOREIGN KEY (campaign_id) REFERENCES kampanie (id)');
        $this->addSql('ALTER TABLE campaign_open ADD CONSTRAINT FK_A597B8469395C3F3 FOREIGN KEY (customer_id) REFERENCES klienci (id)');
        $this->addSql('ALTER TABLE campaign_open ADD CONSTRAINT FK_A597B846EB30F5C8 FOREIGN KEY (campaign_sent_id) REFERENCES campaign_sent (id)');
        $this->addSql('ALTER TABLE campaign_sent ADD CONSTRAINT FK_BB23114BF639F774 FOREIGN KEY (campaign_id) REFERENCES kampanie (id)');
        $this->addSql('ALTER TABLE campaign_sent ADD CONSTRAINT FK_BB23114B9395C3F3 FOREIGN KEY (customer_id) REFERENCES klienci (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE campaign_open DROP FOREIGN KEY FK_A597B846F639F774');
        $this->addSql('ALTER TABLE campaign_open DROP FOREIGN KEY FK_A597B8469395C3F3');
        $this->addSql('ALTER TABLE campaign_open DROP FOREIGN KEY FK_A597B846EB30F5C8');
        $this->addSql('ALTER TABLE campaign_sent DROP FOREIGN KEY FK_BB23114BF639F774');
        $this->addSql('ALTER TABLE campaign_sent DROP FOREIGN KEY FK_BB23114B9395C3F3');
        $this->addSql('DROP TABLE campaign_open');
        $this->addSql('DROP TABLE campaign_sent');
        $this->addSql('DROP TABLE import_history');
        $this->addSql('DROP TABLE kampanie');
        $this->addSql('DROP TABLE klienci');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
