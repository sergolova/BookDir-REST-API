<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\ImagesService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BooksController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ImagesService          $imagesService,
        private BookRepository         $bookRepository
    )
    {
    }

    #[Route('/books', name: 'create_book', methods: 'POST')]
    public function createBook(Request $request): Response
    {
        $status = Response::HTTP_BAD_REQUEST;
        $message = 'Book creating error';
        $result = [];

        try {
            $content = $request->getContent();
            $book = new Book();

            if ($this->bookFromJson($content, $book)) {
                $this->entityManager->persist($book);
                $this->entityManager->flush();

                $status = Response::HTTP_CREATED;
                $message = 'Book created';
                $result['id'] = $book->getId();
            }
        } catch (\Throwable) {
        }

        $result['message'] = $message;

        return $this->json($result, $status);
    }

    #[Route('/books', name: 'get_books', methods: 'GET')]
    public function getBooks(Request $request): Response
    {
        $status = Response::HTTP_BAD_REQUEST;
        $message = 'Get books error';
        $result = [];

        try {
            $page = $request->query->get('page') ?? 1;
            $limit = $request->query->get('limit') ?? 10;
            $offset = ($page - 1) * $limit;

            $books = $this->bookRepository->findBy(
                [], ['id'=>'ASC'], $limit, $offset);

            $message = 'Get books success';
            $status = Response::HTTP_OK;
            $result['books'] = $books;
        } catch (\Throwable) {
        }

        $result['message'] = $message;

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES]);
    }

    #[Route('/books_by_author/{author}', name: 'get_books_by_author', methods: 'GET')]
    public function getBooksByAuthor(string $author, Request $request): Response
    {
        $status = Response::HTTP_BAD_REQUEST;
        $message = 'Find books error';
        $result = [];

        try {
            $page = $request->query->get('page') ?? 1;
            $limit = $request->query->get('limit') ?? 10;
            $offset = ($page - 1) * $limit;

            $books = $this->bookRepository->findByAuthor($author, $limit, $offset);

            $message = 'Get books success';
            $status = Response::HTTP_OK;
            $result['books'] = $books;
        } catch (\Throwable) {
        }

        $result['message'] = $message;

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES]);

    }

    #[Route('/books/{id}', name: 'get_book', methods: 'GET')]
    public function getBook(int $id): Response
    {
        $status = Response::HTTP_BAD_REQUEST;
        $message = 'Get book error';
        $result = [];

        try {
            $book = $this->bookRepository->findOneBy(['id' => $id]);

            if (!$book) {
                $status = Response::HTTP_NOT_FOUND;
            } else {
                $status = Response::HTTP_OK;
                $message = 'Get book success';
                $result['book'] = $book;
            }
        } catch (\Throwable) {
        }

        $result['message'] = $message;

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES]);
    }

    #[Route('/books/{id}', name: 'edit_book', methods: 'PUT')]
    public function editBook(int $id, Request $request): Response
    {
        $status = Response::HTTP_BAD_REQUEST;
        $message = 'Update book error';
        $result = [];

        try {
            $content = $request->getContent();
            $book = $this->bookRepository->findOneBy(['id' => $id]);

            if ($this->bookFromJson($content, $book)) {
                $this->entityManager->persist($book);
                $this->entityManager->flush();

                $status = Response::HTTP_OK;
                $message = 'Update book success';
            }
        } catch (\Throwable) {
        }

        $result['message'] = $message;

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES]);
    }

    public function bookFromJson(?string $json, ?Book $book): bool
    {
        $data = json_decode($json, true);
        $title = trim($data['title']);
        $dateStr = trim($data['publish_date']);

        if ($data && $book && $title && $dateStr) {
            $book->setTitle($title);
            $book->setPublishDate(new DateTime($dateStr));
            $book->setDescription(@$data['description']);

            $fileName = isset($data['image']) ? $this->imagesService->saveImageFromUrl($data['image']) : '';
            $book->setImage($fileName);

            $book->clearAuthors();
            $authors = $this->entityManager->getRepository(Author::class)->findBy(
                ['id' => $data['authors']]);
            foreach ($authors as $a) {
                $book->addAuthor($a);
            }

            return $book->getAuthors()->count() > 0;
        } else {
            return false;
        }
    }
}