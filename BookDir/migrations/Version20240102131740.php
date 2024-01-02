<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240102131740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add demo content';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('INSERT INTO book (id, title, description, image, publish_date) VALUES 
            (101,\'Гарри Поттер и философский камень\', \'Первая книга в серии о молодом волшебнике Гарри Поттере, его приключениях в Хогвартсе и борьбе против темных сил.\', \'0e9a3e0e1697fbbb45136833470942aa.jpg\', \'1998-01-01\'),
            (102,\'Мастер и Маргарита\', \'Фантастический роман, сочетающий аллегорические элементы и сатиру, рассказывающий о приключениях дьявола в Москве.\', \'2e6a9374ca22becccf0c531fe4e1a675.jpg\', \'1928-01-01\'),
            (103,\'Pride and Prejudice\', \'рассказывает о жизни и любви Элизабет Беннет и мистера Дарси в английском обществе начала XIX века\', \'1d73127b3d4a761092a19fbf14afa8e4.jpg\', \'1931-01-01\'),
            (104,\'The Girl with the Dragon Tattoo\', \'Роман расследует таинственное исчезновение молодой наследницы предприятия и вовлекает читателя в мир интриг, заговоров и мести\', \'8261a4d6a0b1b94c6f367d617959f070.jpg\', \'1986-01-01\'),
            (105,\'Идиот\', \'Роман рассказывает о приключениях князя Мышкина, человека, который вернулся из-за границы после лечения. Книга затрагивает темы нравственности, идеализма и конфликтов общества.\', \'f5454629fff8bb0cdeb23d53aa47e8d0.jpg\', \'1869-01-01\')');

        $this->addSql('INSERT INTO author (id, last_name, first_name, patronymic) VALUES 
            (201,\'Остин\', \'Джейн\', \'\'),
            (202,\'Роулинг\', \'Джоан\', \'Кэтлин\'),
            (203,\'Достоевский\', \'Фёдор\', \'Михайлович\'),
            (204,\'Ларссон\', \'Стиг\', \'\'),
            (205,\'Джонсон\', \'Реджер\', \'\')');

        $this->addSql('INSERT INTO book_author (book_id, author_id) VALUES
            (101, 202),
            (102, 203),
            (103, 201),
            (104, 204),
            (104, 205),
            (105, 203)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM book WHERE id >= 100');
        $this->addSql('DELETE FROM author WHERE id >= 200');
    }
}