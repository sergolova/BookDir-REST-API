<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImagesService
{
    const IMAGE_DIR = '/images';
    const IMAGE_ROUTE = '/images';

    public function __construct(
        private readonly ParameterBagInterface $parameters
    )
    {
    }

    public function saveImageFromUrl(?string $imageUrl): string
    {
        if ($imageUrl) {
            $imageContent = file_get_contents($imageUrl);

            if ($imageContent) {
                $fileId = md5($imageContent) . '.' . pathinfo($imageUrl, PATHINFO_EXTENSION);
                $dir = $this->parameters->get('kernel.project_dir') . self::IMAGE_DIR;

                if (!file_exists($dir)) {
                    mkdir($dir, 0775, true);
                }

                if (file_put_contents($dir . '/' . $fileId, $imageContent) !== false) {
                    return $fileId;
                }
            }
        }

        return '';
    }

    public function getImageRoute(string $fileId): string|false
    {
        $file = $this->parameters->get('kernel.project_dir') . self::IMAGE_DIR . '/' . $fileId;
        $res =  self::IMAGE_ROUTE . '/' . $fileId;

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