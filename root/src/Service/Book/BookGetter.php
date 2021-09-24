<?php

namespace App\Service\Book;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;

class BookGetter {

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(array $inputData, Book $book = null): Book
    {
        if (!$book) {
            $book = new Book();
        }

        $book->setName($inputData[Book::NAME]);
        $book->setPublishYear($inputData[Book::PUBLISH_YEAR]);
        $book->setIsbn($inputData[Book::ISBN]);
        $book->setNumberPages($inputData[Book::NUMBER_PAGES]);

        /**@var AuthorRepository $authorRepo*/
        $authorRepo = $this->entityManager->getRepository(Author::class);
        foreach ($inputData[Book::AUTHORS] as $authorId) {
            $book->addAuthor($authorRepo->getAuthorById($authorId));
        }

        return $book;
    }
}
