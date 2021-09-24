<?php

namespace App\Service\Book;

use App\Entity\Book;
use App\Exception\ApplicationException;
use Doctrine\ORM\EntityManagerInterface;

class BookService {

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws ApplicationException
     */
    public function create(array $inputData): Book
    {
        $inputData = BookSanitizer::sanitizeData($inputData);
        $errors = (new BookValidator($this->entityManager))->validateCreateData($inputData);
        $this->throwExceptionIfErrors($errors);
        $book = (new BookGetter($this->entityManager))->create($inputData);
        $this->persistAndFlush($book);

        return $book;
    }

    /**
     * @throws ApplicationException
     */
    public function update(array $inputData): Book
    {
        $inputData = BookSanitizer::sanitizeData($inputData);
        $errors = (new BookValidator($this->entityManager))->validateUpdateData($inputData);
        $this->throwExceptionIfErrors($errors);
        $book = $this->getBookById($inputData[Book::ID]);
        $book = (new BookGetter($this->entityManager))->create($inputData, $book);
        $this->persistAndFlush($book);

        return $book;
    }

    /**
     * @throws ApplicationException
     */
    public function delete(array $inputData): Book
    {
        $inputData = BookSanitizer::sanitizeDeleteData($inputData);
        $errors = (new BookValidator($this->entityManager))->validateDeleteData($inputData);
        $this->throwExceptionIfErrors($errors);
        $book = $this->getBookById($inputData[Book::ID]);
        $book->setDeletedAt(new \DateTime());
        $this->persistAndFlush($book);

        return $book;
    }

    /**
     * @throws ApplicationException
     */
    public function get(array $inputData): Book
    {
        $inputData = BookSanitizer::sanitizeDeleteData($inputData);
        $errors = (new BookValidator($this->entityManager))->validateDeleteData($inputData);
        $this->throwExceptionIfErrors($errors);

        return $this->getBookById($inputData[Book::ID]);
    }

    /**
     * @throws ApplicationException
     */
    private function throwExceptionIfErrors(array $errors)
    {
        if ($errors) {
            $e = new ApplicationException();
            $e->setErrors($errors);

            throw $e;
        }
    }

    private function persistAndFlush(Book $book)
    {
        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }

    private function getBookById(int $id): Book
    {
        return $this->entityManager->getRepository(Book::class)->find($id);
    }
}
