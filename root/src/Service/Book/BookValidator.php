<?php

namespace App\Service\Book;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\AuthorRepository;
use App\Service\ValidatorHelper;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Isbn\Isbn;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class BookValidator {

    private $entityManager;
    private $validator;
    private $isbn;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->validator = Validation::createValidator();
        $this->isbn = new Isbn();
    }

    public function validateCreateData(array $inputData): array
    {
        $assertCollection = new Assert\Collection($this->getCreateConstraints($inputData));
        $violations = $this->validator->validate($inputData, $assertCollection);
        $errors = ValidatorHelper::violationsToArrayOfStrings($violations);

        if ($errors) {
            return $errors;
        }

        return $this->validate($inputData, $errors);
    }

    public function validateUpdateData(array $inputData): array
    {
        if (!isset($inputData[Book::ID])) {
            return ['ID книги не введен'];
        }

        if (!$this->isActiveBookById($inputData[Book::ID])) {
            return ['Активная книга с заданным ID не найдена или удалена'];
        }

        $assertCollection = new Assert\Collection($this->getUpdateConstraints($inputData));
        $violations = $this->validator->validate($inputData, $assertCollection);
        $errors = ValidatorHelper::violationsToArrayOfStrings($violations);

        if ($errors) {
            return $errors;
        }

        return $this->validate($inputData, $errors);
    }

    public function validateDeleteData(array $inputData): array
    {
        if (!$this->isActiveBookById($inputData[Book::ID])) {
            return ['Активная книга с заданным ID не найдена или удалена'];
        }

        return [];
    }

    private function getAllConstraints(): array
    {
        return array_merge($this->getIdConstraint(), [
            Book::AUTHORS => [
                [
                    new Assert\NotNull(),
                    new Assert\Type('array'),
                    new Assert\Count(['min' => 1]),
                ]
            ],
            Book::NAME => [new Assert\Length(['min' => 2, 'allowEmptyString' => false]), new Assert\Length(['max' => 200]), new Assert\NotBlank],
            Book::PUBLISH_YEAR => [new Assert\Length(['min' => 4, 'allowEmptyString' => false]), new Assert\Length(['max' => 4]), new Assert\NotBlank, new Assert\Positive],
            Book::ISBN => [new Assert\Length(['min' => 10, 'allowEmptyString' => false]), new Assert\Length(['max' => 20]), new Assert\NotBlank],
            Book::NUMBER_PAGES => [new Assert\Length(['min' => 1, 'allowEmptyString' => false]), new Assert\Length(['max' => 4]), new Assert\NotBlank, new Assert\Positive],
        ]);
    }

    private function getIdConstraint(): array
    {
        return [Book::ID => [new Assert\Length(['min' => 1, 'allowEmptyString' => false]), new Assert\Length(['max' => 10]), new Assert\NotBlank, new Assert\Positive]];
    }

    private function getCreateConstraints(): array
    {
        $constraints = $this->getAllConstraints();
        unset($constraints[Book::ID]);

        return $constraints;
    }

    private function getUpdateConstraints(): array
    {
        return $this->getAllConstraints();
    }

    private function isUniqueByNameAndIsbn(array $inputData, ?int $excludeId = null): bool
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq(Book::NAME, $inputData[Book::NAME]))
            ->andWhere($criteria->expr()->eq(Book::ISBN, $inputData[Book::ISBN]))
            ->andWhere($criteria->expr()->eq(Book::DELETED_AT, null))
            ->setMaxResults(1);

        if ($excludeId) {
            $criteria->andWhere($criteria->expr()->neq(Book::ID, $inputData[Book::ID]));
        }

        $books = $this->entityManager->getRepository(Book::class)->matching($criteria);

        return $books->count() === 0;
    }

    private function isUniqueByNameAndPublishYear(array $inputData, ?int $excludeId = null): bool
    {
        $criteria = new Criteria();
        $criteria
            ->where($criteria->expr()->eq(Book::NAME, $inputData[Book::NAME]))
            ->andWhere($criteria->expr()->eq(Book::PUBLISH_YEAR, $inputData[Book::PUBLISH_YEAR]))
            ->andWhere($criteria->expr()->eq(Book::DELETED_AT, null))
            ->setMaxResults(1);

        if ($excludeId) {
            $criteria->andWhere($criteria->expr()->neq(Book::ID, $inputData[Book::ID]));
        }

        $books = $this->entityManager->getRepository(Book::class)->matching($criteria);

        return $books->count() === 0;
    }

    private function isActiveBookById(int $id): bool
    {
        return (bool)$this->entityManager->getRepository(Book::class)->findOneBy([
            Book::ID => $id,
            Book::DELETED_AT => null,
        ]);
    }

    private function validate(array $inputData, array $errors): array
    {
        if (!$this->isbn->validation->isbn((string)$inputData[Book::ISBN])) {
            $errors[Book::ISBN] = 'Введеный ISBN невалидный';
        }

        $excludeId = $inputData[Book::ID] ?? null;

        if (!$this->isUniqueByNameAndIsbn($inputData, $excludeId)) {
            $errors[Book::NAME] = 'Активная книга с заданным названием и ISBN уже существует';
        }

        if (!$this->isUniqueByNameAndPublishYear($inputData, $excludeId)) {
            $errors[Book::NAME] = 'Активная книга с заданным названием и годом издания уже существует';
        }

        foreach ($inputData[Book::AUTHORS] as $authorId) {
            if (!$this->existAuthor($authorId)) {
                $errors['authors'] = 'Активный автор с введенным ID='.$authorId.' не существует';

                return $errors;
            }
        }

        return $errors;
    }

    private function existAuthor(int $authorId): bool
    {
        /**@var AuthorRepository $authorRepo*/
        $authorRepo = $this->entityManager->getRepository(Author::class);

        return (bool)$authorRepo->getAuthorById($authorId);
    }
}
