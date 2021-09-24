<?php

use App\Entity\Book as Book;
use App\Exception\ApplicationException;
use App\Service\Book\BookService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class BaseTestClass extends KernelTestCase
{
    protected $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
        $this->entityManager = null;
    }
}
