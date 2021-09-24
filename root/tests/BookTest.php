<?php

use App\Entity\Book as Book;
use App\Exception\ApplicationException;
use App\Service\Book\BookService;

require_once('./tests/BaseTestClass.php');

class BookTest extends BaseTestClass
{
    public function testCreateBookWithInvalidIsbn()
    {
        $inputData = [
            Book::NAME => 'Чистый код',
            Book::PUBLISH_YEAR => 2008,
            Book::ISBN => '111111111111',
            Book::NUMBER_PAGES => 464
        ];

        $errors = [];

        try {
            $bookService = new BookService($this->entityManager);
            $bookService->create($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $this->assertNotEmpty($errors);
    }

    public function testCreateBookWithInvalidData()
    {
        $inputData = [
            Book::NAME => '',
            Book::PUBLISH_YEAR => '',
            Book::ISBN => '111111111111',
            Book::NUMBER_PAGES => 0
        ];

        $errors = [];

        try {
            $bookService = new BookService($this->entityManager);
            $bookService->create($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $this->assertNotEmpty($errors);
    }

    public function testCreateBook()
    {
        $inputData = [
            Book::NAME => 'Чистый код',
            Book::PUBLISH_YEAR => 2008,
            Book::ISBN => '9785498073811',
            Book::NUMBER_PAGES => 464,
            Book::AUTHORS => [1],
        ];

        $errors = [];

        try {
            $bookService = new BookService($this->entityManager);
            $bookService->create($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
            var_dump($errors);
        }

        $this->assertEmpty($errors);
    }

    public function testCreateBookIsbnWithHyphens()
    {
        $inputData = [
            Book::NAME => 'Чистая архитектура',
            Book::PUBLISH_YEAR => 2018,
            Book::ISBN => '978-5-4461-0772-8',
            Book::NUMBER_PAGES => 352,
            Book::AUTHORS => [
                1
            ]
        ];

        $errors = [];

        try {
            $bookService = new BookService($this->entityManager);
            $bookService->create($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
            var_dump($errors);
        }

        $this->assertEmpty($errors);
    }

    public function testDeleteBook()
    {
        $inputData = [
            Book::ID => 1
        ];

        $errors = [];

        try {
            $bookService = new BookService($this->entityManager);
            $bookService->delete($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $this->assertEmpty($errors);
    }

    public function testUpdateBookWithoutId()
    {
        $inputData = [
            Book::NAME => 'Чистый код',
            Book::PUBLISH_YEAR => 2008,
            Book::ISBN => '9785498073811',
            Book::NUMBER_PAGES => 111
        ];

        $errors = [];

        try {
            $bookService = new BookService($this->entityManager);
            $bookService->update($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $this->assertNotEmpty($errors);
    }

    public function testUpdateBookNumberPages()
    {
        $inputData = [
            Book::ID => 2,
            Book::NAME => 'Чистый код',
            Book::PUBLISH_YEAR => 2008,
            Book::ISBN => '9785498073811',
            Book::NUMBER_PAGES => 111,
            Book::AUTHORS => [
                1
            ]
        ];

        $errors = [];

        try {
            $bookService = new BookService($this->entityManager);
            $bookService->update($inputData);
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $this->assertEmpty($errors);
        $this->expectOutputString('');
    }
}
