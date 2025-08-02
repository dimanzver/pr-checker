<?php

declare(strict_types=1);

namespace App\GithubApi\Dto;

/**
 * Данные о PR
 */
class PullRequestDto
{
    public function __construct(
        public readonly int $number,
        public readonly string $title,
        public readonly string $url,
        /** Название репозитория вида :owner/:repoName */
        public readonly string $repositoryNameWithOwner,
        public readonly string $mergeable,
    )
    { }
}