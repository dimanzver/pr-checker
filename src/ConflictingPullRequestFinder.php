<?php

declare(strict_types=1);

namespace App;

use App\GithubApi\Dto\PullRequestDto;
use App\GithubApi\GithubClient;

class ConflictingPullRequestFinder
{
    private GithubApi\GithubClient $githubClient;

    /** @var string[] Массив имён репозиториев вида :owner/:repoName, по которым ищем PR-ы */
    private array $repositoryNames = [];

    /**
     * @var int[] ID PR-ов, которые исключаем из выборки
     */
    private array $excludedPullRequestIds = [];

    public static function createFromEnv(): static
    {
        $finder = new static();
        $finder->githubClient = new GithubClient($_ENV['GITHUB_TOKEN']);
        $finder->repositoryNames = explode(',', $_ENV['GITHUB_REPOSITORIES'] ?? '');
        $finder->excludedPullRequestIds = array_map('intval', explode(',', $_ENV['EXCLUDED_PULL_REQUEST_IDS'] ?? ''));
        return $finder;
    }

    /**
     * Ищем PR-ы с конфликтами и репортим
     * @return void
     */
    public function findAndReport()
    {
        $pullRequests = $this->find();
        $notifyPath = dirname(__DIR__) . '/notify.sh';
        foreach ($pullRequests as $pullRequest) {
            exec("$notifyPath \"$pullRequest->title\" \"$pullRequest->url\" &");
        }
    }

    /**
     * Ищем PR-ы с конфликтами
     * @return PullRequestDto[]
     */
    private function find(): array
    {
        $pullRequests = $this->githubClient->getMyPullRequests($this->repositoryNames);

        $result = [];
        foreach ($pullRequests as $pullRequest) {
            if ($pullRequest->mergeable !== 'CONFLICTING' || in_array($pullRequest->number, $this->excludedPullRequestIds)) {
                continue;
            }

            $result[] = $pullRequest;
        }
        return $result;
    }
}