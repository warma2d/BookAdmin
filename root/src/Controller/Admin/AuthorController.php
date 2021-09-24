<?php

namespace App\Controller\Admin;

use App\Entity\Author;
use App\Exception\ApplicationException;
use App\Form\AuthorType;
use App\Service\Author\AuthorService;
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
class AuthorController extends AbstractController
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
     * @Route("/authors", methods="GET", name="authors")
     */
    public function authors(): Response
    {
        $authors = $this->entityManager->getRepository(Author::class)->findBy([Author::DELETED_AT => null]);

        return $this->render('admin/pages/author/authors.html.twig', [
            'authors' => $authors
        ]);
    }

    /**
     * @Route("/author/add", methods="GET|POST", name="author_add")
     */
    public function addAuthor(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        $errors = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $data = $data['author'] ?? [];
            try {
                (new AuthorService($this->entityManager))->create($data);
                $this->addFlash('success', 'Автор успешно добавлен');

                return $this->redirectToRoute('app_admin_authors');
            } catch (ApplicationException $exception) {
                $errors = $exception->getErrors();
            }
        }

        return $this->render('admin/pages/author/author.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

    /**
     * @Route("/author/{id}", requirements={"id":"\d+"}, methods="GET|POST", name="author_edit")
     * @param  Request  $request
     * @param  Author  $author
     * @return Response
     */
    public function editAuthor(Request $request, Author $author): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $request->request->all();
            $data = $data['author'] ?? [];
            $data[Author::ID] = $author->getId();

            try {
                (new AuthorService($this->entityManager))->update($data);
                $this->addFlash('success', 'Автор успешно обновлен');
            } catch (ApplicationException $exception) {
                return $this->render('admin/pages/author/author.html.twig', [
                    'form' => $form->createView(),
                    'errors' => $exception->getErrors()
                ]);
            }

            return $this->redirectToRoute('app_admin_authors');
        }

        return $this->render('admin/pages/author/author.html.twig', [
            'form' => $form->createView(),
            'errors' => []
        ]);
    }

    /**
     * @Route("/author/{id}/delete", requirements={"id":"\d+"}, methods="GET", name="author_delete")
     * @param  Request  $request
     * @param  Author  $author
     * @return Response
     */
    public function deleteAuthor(Request $request, Author $author): Response
    {
        $data = $request->request->all();
        $data = $data['author'] ?? [];
        $data[Author::ID] = $author->getId();

        $errors = [];

        try {
            (new AuthorService($this->entityManager))->delete($data);
            $this->addFlash('success', 'Автор успешно удален');
            return $this->redirectToRoute('app_admin_authors');
        } catch (ApplicationException $exception) {
            $errors = $exception->getErrors();
        }

        $authors = $this->entityManager->getRepository(Author::class)->findBy([Author::DELETED_AT => null]);

        return $this->render('admin/pages/author/authors.html.twig', [
            'authors' => $authors,
            'errors' => $errors
        ]);
    }

}
