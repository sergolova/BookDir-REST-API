<?php

namespace App\Controller;

use App\DTO\BookDto;
use App\Entity\Author;
use App\Entity\Book;
use App\Repository\BookRepository;
use App\Service\ImagesService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
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
    public function createBook(#[MapRequestPayload] BookDto $bookDto): Response
    {
        try {
            $book = new Book();
            $this->bookFromDto($book, $bookDto);

            $this->entityManager->persist($book);
            $this->entityManager->flush();

            $status = Response::HTTP_CREATED;
            $result = $book;
        } catch (\Exception $e) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $result['error'] = $e->getMessage();
        }

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT]);
    }

    #[Route('/books/{limit}/{page}', name: 'get_books',
        requirements: ['page' => '\d+', 'limit' => '\d+'],
        defaults: ['page' => 1, 'limit' => 10],
        methods: 'GET')]
    public function getBooks(int $limit, int $page): Response
    {
        try {
            $offset = ($page - 1) * $limit;

            $books = $this->bookRepository->findBy(
                [], ['id' => 'ASC'], $limit, $offset);

            $status = Response::HTTP_OK;
            $result = $books;
        } catch (\Exception $e) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $result['error'] = $e->getMessage();
        }

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT]);
    }

    #[Route('/books_by_author/{author}/{limit}/{page}', name: 'get_books_by_author',
        requirements: ['page' => '\d+', 'limit' => '\d+', 'author' => '[^/]+'],
        defaults: ['page' => 1, 'limit' => 10],
        methods: 'GET')]
    public function getBooksByAuthor(string $author, int $limit, int $page): Response
    {
        try {
            $offset = ($page - 1) * $limit;
            $books = $this->bookRepository->findByAuthor($author, $limit, $offset);

            $status = Response::HTTP_OK;
            $result['books'] = $books;
        } catch (\Exception $e) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $result['error'] = $e->getMessage();
        }

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT]);

    }

    #[Route('/book/{id}', name: 'get_book', requirements: ['id' => '\d+'], methods: 'GET')]
    public function getBook(int $id): Response
    {
        try {
            $book = $this->bookRepository->findOneBy(['id' => $id]);

            if (!$book) {
                return new Response(null, Response::HTTP_NOT_FOUND);
            } else {
                $status = Response::HTTP_OK;
                $result = $book;
            }
        } catch (\Exception $e) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $result['error'] = $e->getMessage();
        }

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT]);
    }

    #[Route('/book/{id}', name: 'edit_book', methods: 'PUT')]
    public function editBook(int $id, #[MapRequestPayload] BookDto $bookDto): Response
    {
        try {
            $book = $this->bookRepository->findOneBy(['id' => $id]);
            if (!$book) {
                throw new Exception('Book not found');
            }
            $this->bookFromDto($book, $bookDto);
            $this->entityManager->persist($book);
            $this->entityManager->flush();

            $status = Response::HTTP_OK;
            $result = $book;
        } catch (\Exception $e) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $result['error'] = $e->getMessage();
        }

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT]);
    }

    /**
     * @throws \Exception
     */
    public function bookFromDto(Book $book, BookDto $bookDto): void
    {
        $book->setTitle($bookDto->title);
        $book->setPublishDate(new DateTime($bookDto->publish_date));
        $book->setDescription($bookDto->description);

        $book->clearAuthors();
        $authors = $this->entityManager->getRepository(Author::class)->findBy(
            ['id' => $bookDto->authors]);

        if (count($authors) < count($bookDto->authors)) {
            throw new Exception('Author not exists');
        }

        foreach ($authors as $a) {
            $book->addAuthor($a);
        }

        $fileName = $this->imagesService->saveImageFromUrl($bookDto->image);
        $book->setImage($fileName);
    }
}