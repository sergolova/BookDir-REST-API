<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImagesService
{
    const IMAGE_DIR = '/images';
    const IMAGE_ROUTE = '/images';

    private string $imageDir;

    public function __construct(
        private readonly ParameterBagInterface $parameters
    )
    {
        $this->imageDir = $this->parameters->get('kernel.project_dir') . self::IMAGE_DIR;

        if (!file_exists($this->imageDir)) {
            mkdir($this->imageDir, 0775, true);
        }
    }

    /**
     * @throws \Exception
     */
    public function saveImageFromUrl(string $imageUrl): string
    {
        $options  = ['http' => ['user_agent' => 'curl/7.64.1']];
        $context  = stream_context_create($options);
        $imageContent = file_get_contents($imageUrl,false, $context);

        if ($imageContent === false) {
            throw new \Exception('Failed to fetch image content');
        }

        $fileId = md5($imageContent) . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);

        if (file_put_contents($this->imageDir . '/' . $fileId, $imageContent) !== false) {
            return $fileId;
        } else {
            throw new \Exception('Failed to save image content');
        }
    }

    public function getImageRoute(string $fileId): string|false
    {
        $file = $this->parameters->get('kernel.project_dir') . self::IMAGE_DIR . '/' . $fileId;
        $res = self::IMAGE_ROUTE . '/' . $fileId;

        return $fileId && file_exists($file) ? $res : false;
    }

    public function getImageFullName(string $fileId): string|false
    {
        $res = $this->parameters->get('kernel.project_dir') . self::IMAGE_DIR . '/' . $fileId;

        return $fileId && file_exists($res) ? $res : false;
    }

    public function getImageContent(string $fileId): string|false
    {
        $file = $this->getImageFullName($fileId);

        return $file ? file_get_contents($file) : false;
    }
}