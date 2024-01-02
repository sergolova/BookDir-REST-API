<?php

namespace App\Controller;

use App\Service\ImagesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImagesController extends AbstractController
{
    #[Route('/images/{id}', name: 'app_images', methods: 'GET')]
    public function showImage(string $id, ImagesService $imagesService): Response
    {
        $content = $imagesService->getImageContent($id);

        if ($content) {
            $response = new Response($content, Response::HTTP_OK);
            $ext = strtolower(pathinfo($id, PATHINFO_EXTENSION));
            $mimeType = $ext === 'png' ? 'image/png' : 'image/jpeg';
            $response->headers->set('Content-Type', $mimeType);
        } else {
            $response = new Response(null, Response::HTTP_NOT_FOUND);
        }

        return $response;
    }
}