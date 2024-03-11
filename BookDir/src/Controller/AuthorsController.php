<?php

namespace App\Controller;

use App\DTO\AuthorDto;
use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;


class AuthorsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthorRepository       $authorRepository
    )
    {
    }

    #[Route('/authors', name: 'create_author', methods: 'POST')]
    public function createAuthor(#[MapRequestPayload] AuthorDto $authorDto): Response
    {
        try {
            $author = new Author();
            $author->setFirstName($authorDto->first_name);
            $author->setLastName($authorDto->last_name);
            $author->setPatronymic($authorDto->patronymic);

            $this->entityManager->persist($author);
            $this->entityManager->flush();

            $status = Response::HTTP_CREATED;
            $result = $author;
        } catch (\Exception $e) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $result['error'] = $e->getMessage();
        }

        return $this->json($result, $status);
    }

    #[Route('/authors/{limit}/{page}', name: 'get_authors',
        requirements: ['page' => '\d+', 'limit' => '\d+'],
        defaults: ['page' => 1, 'limit' => 10],
        methods: 'GET')]
    public function getAuthors(int $limit, int $page): Response
    {
        $result = [];

        try {
            $offset = ($page - 1) * $limit;

            $authors = $this->authorRepository->findBy(
                [], ['id' => 'ASC'], $limit, $offset);

            $status = Response::HTTP_OK;
            $result = $authors;
        } catch (\Exception $e) {
            $status = Response::HTTP_INTERNAL_SERVER_ERROR;
            $result['error'] = $e->getMessage();
        }

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT]);
    }
}