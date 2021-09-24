<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Exception\ApplicationException;
use App\Form\BookType;
use App\Service\Book\BookService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/admin", name="app_admin_")
 */
class BookController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/books", methods="GET", name="books")
     */
    public function books(): Response
    {
        $books = $this->entityManager->getRepository(Book::class)->findBy([Book::DELETED_AT => null]);

        return $this->render('admin/pages/book/books.html.twig', [
            'books' => $books
        ]);
    }

    /**
     * @Route("/book/add", methods="GET|POST", name="book_add")
     */
    public function addBook(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        $errors = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $data = $data['book'] ?? [];

            try {
                (new BookService($this->entityManager))->create($data);
                $this->addFlash('success', 'Книга успешно создана');

                return $this->redirectToRoute('app_admin_books');
            } catch (ApplicationException $exception) {
                $errors = $exception->getErrors();
            }
        }

        return $this->render('admin/pages/book/book.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

    /**
     * @Route("/book/{id}", requirements={"id":"\d+"}, methods="GET|POST", name="book_edit")
     * @param  Request  $request
     * @param  Book  $book
     * @return Response
     */
    public function editBook(Request $request, Book $book): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $data = $data['book'] ?? [];
            $data[Book::ID] = $book->getId();

            try {
                (new BookService($this->entityManager))->update($data);
                $this->addFlash('success', 'Книга успешно обновлена');
            } catch (ApplicationException $exception) {
                return $this->render('admin/pages/book/book.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $exception->getErrors()
                ]);
            }

            return $this->redirectToRoute('app_admin_books');
        }

        return $this->render('admin/pages/book/book.html.twig', [
            'form' => $form->createView(),
            'errors' => []
        ]);
    }

    /**
     * @Route("/book/{id}/delete", requirements={"id":"\d+"}, methods="GET", name="book_delete")
     * @param  Request  $request
     * @param  Book  $book
     * @return Response
     */
    public function deleteBook(Request $request, Book $book): Response
    {
        $data = $request->request->all();
        $data = $data['book'] ?? [];
        $data[Book::ID] = $book->getId();

        $errors = [];

        try {
            (new BookService($this->entityManager))->delete($data);
            $this->addFlash('success', 'Книга успешно удалена');
            return $this->redirectToRoute('app_admin_books');
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $books = $this->entityManager->getRepository(Book::class)->findBy([Book::DELETED_AT => null]);

        return $this->render('admin/pages/book/books.html.twig', [
            'books' => $books,
            'errors' => $errors
        ]);
    }

}
