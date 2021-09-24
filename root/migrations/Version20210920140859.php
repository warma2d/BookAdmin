<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210920140859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE IF NOT EXISTS author
                            (
                                id         INT AUTO_INCREMENT NOT NULL,
                                name       VARCHAR(255)       NOT NULL,
                                surname    VARCHAR(255)       NOT NULL,
                                patronymic VARCHAR(255) DEFAULT NULL,
                                created_at datetime default NOW() not null,
                                deleted_at datetime default null null,    
                                PRIMARY KEY (id)
                                /* , UNIQUE KEY `uq_author_name_surname_patronymic` (`name`, `surname`, `patronymic`) */
                            ) DEFAULT CHARACTER SET utf8mb4
                              COLLATE `utf8mb4_unicode_ci`
                              ENGINE = InnoDB');

        $this->addSql('CREATE TABLE IF NOT EXISTS book (
    
    id            INT AUTO_INCREMENT NOT NULL,
    name          VARCHAR(255)       NOT NULL,
    publish_year  SMALLINT           NOT NULL,
    isbn         VARCHAR(13)        NOT NULL,
    number_pages SMALLINT           NOT NULL,
    created_at datetime default NOW() not null,
    deleted_at datetime default null null,
    PRIMARY KEY (id)
    /* , UNIQUE KEY `uq_book_name_isbn` (`name`, `isbn`),
    UNIQUE KEY `uq_book_name_publishYear` (`name`, `publish_year`)*/
    ) DEFAULT CHARACTER SET utf8mb4
    COLLATE `utf8mb4_unicode_ci`
    ENGINE = InnoDB
');
        $this->addSql('CREATE TABLE IF NOT EXISTS book_author (book_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_9478D34516A2B381 (book_id), INDEX IDX_9478D345F675F31B (author_id), PRIMARY KEY(book_id, author_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D34516A2B381 FOREIGN KEY (book_id) REFERENCES book (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_author ADD CONSTRAINT FK_9478D345F675F31B FOREIGN KEY (author_id) REFERENCES author (id) ON DELETE CASCADE');

        $this->addSql('CREATE TABLE IF NOT EXISTS `user` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `roles` varchar(200) NOT NULL,
                        `email` varchar(200)  NOT NULL,
                        `password` varchar(50)  NOT NULL,
                        created_at datetime default NOW() not null,
                        PRIMARY KEY (`id`),
                        UNIQUE KEY `uq_user_email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');

        $this->addSql('INSERT INTO `user` (roles, email, password, created_at) VALUES (\'["ROLE_ADMIN"]\', \'admin@mail.ru\', \'N883D0t+h4Cc0HxEE7rKlg==\', \'2021-09-22 05:41:44\');');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_author DROP FOREIGN KEY FK_9478D345F675F31B');
        $this->addSql('ALTER TABLE book_author DROP FOREIGN KEY FK_9478D34516A2B381');
        $this->addSql('DROP TABLE author');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_author');
        $this->addSql('DROP TABLE `user`');
    }
}
