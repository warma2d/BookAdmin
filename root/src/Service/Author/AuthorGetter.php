<?php

namespace App\Service\Author;

use App\Entity\Author;

class AuthorGetter {

    public static function create(array $inputData, Author $author = null): Author
    {
        if (!$author) {
            $author = new Author();
        }

        $author->setName($inputData[Author::NAME]);
        $author->setSurname($inputData[Author::SURNAME]);
        $author->setPatronymic($inputData[Author::PATRONYMIC]);

        return $author;
    }
}
