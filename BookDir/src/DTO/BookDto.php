<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class BookDto
{
    public function __construct(
        #[Assert\Type('string', message: 'Title must be a string')]
        #[Assert\Length(max: 255, maxMessage: 'Title must contain no more than 255 characters')]
        #[Assert\NotBlank(message: 'Title is required')]
        public string  $title,

        #[Assert\NotBlank(message: 'Publish date is required')]
        #[Assert\Date(message: 'Publish date should be a valid date')]
        public string  $publish_date,

        #[Assert\Type(['string', 'null'], message: 'Description must be a string or null')]
        #[Assert\NotBlank(message: 'Description is required', allowNull: true)]
        #[Assert\Length(max: 255, maxMessage: 'Description should be less than 255 characters')]
        public ?string $description,

        #[Assert\Url(message: 'Image is not a valid URL')]
        public ?string $image,

        #[Assert\All([
            new Assert\Type('int', message: 'Authors must be an array')],
        )]
        #[Assert\Count(min: 1, minMessage: 'Authors should be at least 1 author')]
        #[Assert\Count(max: 100, maxMessage: 'Authors must contain no more than 100 elements')]
        public array   $authors,
    )
    {
        $this->title = trim($title);
        $this->description = trim($description);
        $this->authors = array_unique($this->authors);
    }
}