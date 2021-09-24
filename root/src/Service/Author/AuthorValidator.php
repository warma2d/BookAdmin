<?php

namespace App\Service\Author;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Service\ValidatorHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validation;

class AuthorValidator {

    private $entityManager;
    private $validator;

    /**@var AuthorRepository */
    private $authorRepo;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->validator = Validation::createValidator();
        $this->authorRepo = $this->entityManager->getRepository(Author::class);
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
        if (!$this->authorRepo->getAuthorById($inputData[Author::ID])) {
            return ['Активный автор с заданным ID не найден или удалён'];
        }

        $violations = $this->validator->validate($inputData, new Assert\Collection($this->getUpdateConstraints()));
        $errors = ValidatorHelper::violationsToArrayOfStrings($violations);

        if ($errors) {
            return $errors;
        }

        return $this->validate($inputData, $errors);
    }

    public function validateDeleteData(array $inputData): array
    {
        $author = $this->authorRepo->getAuthorById($inputData[Author::ID]);
        if (!$author) {
            return ['Активный автор с заданным ID не найден или удалён'];
        }

        if (!$author->getActiveBooks()->isEmpty()) {
            return ['У автора есть активные книги'];
        }

        return [];
    }

    private function getAllConstraints(): array
    {
        return array_merge($this->getIdConstraint(), [
            Author::NAME => [new Assert\Length(['min' => 2, 'allowEmptyString' => false]), new Assert\Length(['max' => 200]), new Assert\NotBlank],
            Author::SURNAME => [new Assert\Length(['min' => 2, 'allowEmptyString' => false]), new Assert\Length(['max' => 200]), new Assert\NotBlank],
            Author::PATRONYMIC => [new Assert\Length(['min' => 2, 'allowEmptyString' => true]), new Assert\Length(['max' => 200])],
        ]);
    }

    private function getIdConstraint(): array
    {
        return [Author::ID => [new Assert\Length(['min' => 1, 'allowEmptyString' => false]), new Assert\Length(['max' => 10]), new Assert\NotBlank, new Assert\Positive]];
    }

    private function getCreateConstraints(): array
    {
        $constraints = $this->getAllConstraints();
        unset($constraints[Author::ID]);

        return $constraints;
    }

    private function getUpdateConstraints(): array
    {
        return $this->getAllConstraints();
    }

    private function isUnique(array $inputData): bool
    {
        return !$this->entityManager->getRepository(Author::class)->findOneBy([
            Author::NAME => $inputData[Author::NAME],
            Author::SURNAME => $inputData[Author::SURNAME],
            Author::PATRONYMIC => $inputData[Author::PATRONYMIC],
            Author::DELETED_AT => null,
        ]);
    }

    private function validate(array $inputData, array $errors): array
    {
        if (!$this->isUnique($inputData)) {
            $errors[Author::NAME] = 'Активный автор с введеным ФИО уже существует!';
        }

        return $errors;
    }
}
