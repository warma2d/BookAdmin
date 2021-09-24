<?php

use App\Entity\Author as Author;
use App\Exception\ApplicationException;
use App\Service\Author\AuthorService;

require_once('./tests/BaseTestClass.php');

class AuthorTest extends BaseTestClass
{
    public function testCreateAuthor()
    {
        $inputData = [
            Author::NAME => 'Роберт',
            Author::SURNAME => 'Сесил Мартин',
            Author::PATRONYMIC => ''
        ];

        $errors = [];

        try {
            $authorService = new AuthorService($this->entityManager);
            $author = $authorService->create($inputData);

        } catch (ApplicationException $exception) {
            $author = null;
            $errors = $exception->getErrors();
            var_dump($errors);
        }
        $this->isEmpty($errors);
        $this->assertIsNumeric($author->getId());
    }

    public function testCreateDuplicateAuthor()
    {
        $inputData = [
            Author::NAME => 'Роберт',
            Author::SURNAME => 'Сесил Мартин',
            Author::PATRONYMIC => ''
        ];

        $errors = [];

        try {
            $authorService = new AuthorService($this->entityManager);
            $authorService->create($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $this->assertNotEmpty($errors);
    }

    public function testCreateAnotherAuthor()
    {
        $inputData = [
            Author::NAME => 'Лев',
            Author::SURNAME => 'Толстой',
            Author::PATRONYMIC => 'Николаевич'
        ];

        try {
            $authorService = new AuthorService($this->entityManager);
            $author = $authorService->create($inputData);

        } catch (ApplicationException $exception) {
            $author = null;
        }

        $this->assertIsNumeric($author->getId());
    }

    public function testCreateAuthor3()
    {
        $inputData = [
            Author::NAME => 'Иван',
            Author::SURNAME => 'Пупкин',
            Author::PATRONYMIC => 'Николаевич'
        ];

        try {
            $authorService = new AuthorService($this->entityManager);
            $author = $authorService->create($inputData);

        } catch (ApplicationException $exception) {
            $author = null;
        }

        $this->assertIsNumeric($author->getId());
    }

    public function testUpdateAuthorToDuplicateAuthor()
    {
        $inputData = [
            'id' => 2,
            Author::NAME => 'Роберт',
            Author::SURNAME => 'Сесил Мартин',
            Author::PATRONYMIC => ''
        ];

        $errors = [];

        try {
            $authorService = new AuthorService($this->entityManager);
            $authorService->update($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $this->assertNotEmpty($errors);
    }


    public function testUpdateAuthorToUniqueAuthor()
    {
        $inputData = [
            'id' => 2,
            Author::NAME => 'Николай',
            Author::SURNAME => 'Носов',
            Author::PATRONYMIC => 'Николаевич'
        ];

        $errors = [];

        try {
            $authorService = new AuthorService($this->entityManager);
            $authorService->update($inputData);

        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
            var_dump($errors);
        }

        $this->assertEmpty($errors);
    }

    public function testUpdateAuthorSurname()
    {
        $inputData = [
            'id' => 1,
            Author::SURNAME => 'Чехов',
        ];

        $errors = [];

        try {
            $authorService = new AuthorService($this->entityManager);
            $authorService->update($inputData);

        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $this->assertNotEmpty($errors);
    }

    public function testDeleteAuthorWithoutAssignedBooks()
    {
        $inputData = [
            'id' => 3,
        ];

        $errors = [];

        try {
            $authorService = new AuthorService($this->entityManager);
            $authorService->delete($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
            var_dump($errors);
        }

        $this->assertEmpty($errors);
    }

    public function testGetAuthor()
    {
        $inputData = [
            'id' => 1,
        ];

        $errors = [];

        try {
            $authorService = new AuthorService($this->entityManager);
            $author = $authorService->get($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $this->assertEmpty($errors);
        $this->assertTrue($author->getId() === 1);
    }
}
