<?php

namespace App\Serializer;

use App\Entity\Book;
use App\Service\ImagesService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class BookNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public function __construct(
        private readonly ImagesService $imagesService
    )
    {
    }

    public function normalize($object, string $format = null, array $context = []): array
    {
        $data = [
            'id' => $object->getId(),
            'title' => $object->getTitle(),
            'description' => $object->getDescription(),
            'image' => (string)$this->imagesService->getImageRoute($object->getImage()),
            'publish_date' => $object->getPublishDate()->format('Y-m-d'),
            'authors' => $object->getAuthors()->map(fn($a) => $a->getId())->toArray(),
        ];

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        return $data instanceof Book;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Book::class => true,
        ];
    }
}