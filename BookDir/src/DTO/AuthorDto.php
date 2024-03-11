<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class AuthorDto
{
    public function __construct(
        #[Assert\Type('string', message: 'First name must be a string')]
        #[Assert\Length(max: 255, maxMessage: 'First name must contain no more than 255 characters')]
        #[Assert\NotBlank(message: 'First name is required')]
        public string  $first_name,

        #[Assert\Type('string', message: 'Last name must be a string')]
        #[Assert\NotBlank(message: 'Last name is required')]
        #[Assert\Length(min: 3, minMessage: 'Last name should be at least 3 characters')]
        #[Assert\Length(max: 255, maxMessage: 'Last name must contain no more than 255 characters')]
        public string  $last_name,

        #[Assert\Type(['string', 'null'], message: 'Patronymic must be a string or null')]
        #[Assert\NotBlank(message: 'Patronymic name is required', allowNull: true)]
        #[Assert\Length(max: 255, maxMessage: 'Patronymic should be less than 255 characters')]
        public ?string $patronymic
    )
    {
        $this->first_name = trim($first_name);
        $this->last_name = trim($last_name);
        $this->patronymic = is_string($patronymic) ? trim($patronymic) : null;
    }
}