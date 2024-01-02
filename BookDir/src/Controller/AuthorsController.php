<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AuthorsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthorRepository $authorRepository
    )
    {
    }

    #[Route('/authors', name: 'create_author', methods: 'POST')]
    public function createAuthor(Request $request): Response
    {
        $status = Response::HTTP_BAD_REQUEST;
        $message = 'Author creating error';
        $result = [];

        try {
            $content = $request->getContent();
            $author = new Author();

            if ($this->authorFromJson($content, $author)) {
                $this->entityManager->persist($author);
                $this->entityManager->flush();

                $status = Response::HTTP_CREATED;
                $message = 'Author created';
                $result['id'] = $author->getId();
            }
        } catch (\Throwable) {
        }

        $result['message'] = $message;

        return $this->json($result, $status);
    }

    #[Route('/authors', name: 'get_authors', methods: 'GET')]
    public function getAuthors(Request $request, EntityManagerInterface $entityManager): Response
    {
        $status = Response::HTTP_BAD_REQUEST;
        $message = 'Get authors error';
        $result = [];

        try {
            $page = $request->query->get('page') ?? 1;
            $limit = $request->query->get('limit') ?? 10;
            $offset = ($page - 1) * $limit;

            $authors = $this->authorRepository->findBy(
                [], ['id' => 'ASC'], $limit, $offset);

            $status = Response::HTTP_OK;
            $message = 'Get authors success';
            $result['authors'] = $authors;
        } catch (\Throwable) {
        }

        $result['message'] = $message;

        return $this->json($result, $status, [],
            ['json_encode_options' => JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT]);
    }

    public function authorFromJson(?string $json, ?Author $author): bool
    {
        $data = json_decode($json, true);
        $first_name = trim($data['first_name']);
        $last_name = trim($data['last_name']);

        if ($data && $author && $first_name && strlen($last_name) >= 3) {
            $author->setFirstName($first_name);
            $author->setLastName($last_name);
            $author->setPatronymic(@$data['patronymic']);

            return true;
        } else {
            return false;
        }
    }
}