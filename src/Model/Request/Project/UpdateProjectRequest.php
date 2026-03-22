<?php

declare(strict_types=1);

namespace App\Model\Request\Project;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdateProjectRequest
{
    /**
     * @param string[] $technologies
     */
    public function __construct(
        #[Assert\NotBlank(message: 'Title is required.')]
        #[Assert\Length(min: 2, max: 255, minMessage: 'Title must be at least {{ limit }} characters.')]
        public readonly string $title = '',
        #[Assert\NotBlank(message: 'Description is required.')]
        public readonly string $description = '',
        #[Assert\Url(message: 'Please provide a valid image URL.')]
        public readonly ?string $imageUrl = null,
        #[Assert\Url(message: 'Please provide a valid project URL.')]
        public readonly ?string $projectUrl = null,
        #[Assert\Url(message: 'Please provide a valid repository URL.')]
        public readonly ?string $repoUrl = null,
        #[Assert\All([new Assert\Type('string')])]
        public readonly array $technologies = [],
        #[Assert\Choice(choices: ['active', 'archived'], message: 'Status must be either "active" or "archived".')]
        public readonly string $status = 'active',
    ) {
    }
}
