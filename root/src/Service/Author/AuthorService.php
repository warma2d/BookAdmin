<?php

namespace App\Service\Author;

use App\Entity\Author;
use App\Exception\ApplicationException;
use Doctrine\ORM\EntityManagerInterface;

class AuthorService {

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws ApplicationException
     */
    public function create(array $inputData): Author
    {
        $inputData = AuthorSanitizer::sanitizeData($inputData);
        $errors = (new AuthorValidator($this->entityManager))->validateCreateData($inputData);
        $this->throwExceptionIfErrors($errors);
        $author = AuthorGetter::create($inputData);
        $this->persistAndFlush($author);

        return $author;
    }

    /**
     * @throws ApplicationException
     */
    public function update(array $inputData): Author
    {
        $inputData = AuthorSanitizer::sanitizeData($inputData);
        $errors = (new AuthorValidator($this->entityManager))->validateUpdateData($inputData);
        $this->throwExceptionIfErrors($errors);
        $author = $this->getAuthorById($inputData[Author::ID]);
        $author = AuthorGetter::create($inputData, $author);
        $this->persistAndFlush($author);

        return $author;
    }

    /**
     * @throws ApplicationException
     */
    public function delete(array $inputData): Author
    {
        $inputData = AuthorSanitizer::sanitizeDeleteData($inputData);
        $errors = (new AuthorValidator($this->entityManager))->validateDeleteData($inputData);
        $this->throwExceptionIfErrors($errors);
        $author = $this->getAuthorById($inputData[Author::ID]);
        $author->setDeletedAt(new \DateTime());
        $this->persistAndFlush($author);

        return $author;
    }

    /**
     * @throws ApplicationException
     */
    public function get(array $inputData): Author
    {
        $inputData = AuthorSanitizer::sanitizeDeleteData($inputData);
        $errors = (new AuthorValidator($this->entityManager))->validateDeleteData($inputData);
        $this->throwExceptionIfErrors($errors);

        return $this->getAuthorById($inputData[Author::ID]);
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

    private function persistAndFlush(Author $author)
    {
        $this->entityManager->persist($author);
        $this->entityManager->flush();
    }

    private function getAuthorById(int $id): Author
    {
        return $this->entityManager->getRepository(Author::class)->find($id);
    }
}
