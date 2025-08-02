<?php

declare(strict_types=1);

namespace App\GithubApi;

use App\GithubApi\Dto\PullRequestDto;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\RequestOptions;

/**
 * Класс для работы с гитхаб апи
 */
class GithubClient
{
    protected HttpClient $client;

    public function __construct(
        #[\SensitiveParameter] private string $githubToken
    ) {
        $this->client = new HttpClient([
            RequestOptions::TIMEOUT => 10,
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->githubToken,
            ],
        ]);
    }

    /**
     * @param string $repositoryNames Массив имён репозиториев вида :owner/:repoName
     * @return PullRequestDto[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getMyPullRequests(array $repositoryNames)
    {
        $query = "
            {
              viewer {
                pullRequests(first: 100, states: OPEN) {
                  totalCount
                  nodes {
                    number
                    title
                    baseRepository {
                        nameWithOwner
                    }
                    baseRefName
                    mergeable
                    url
                  }
                  pageInfo {
                    hasNextPage
                    endCursor
                  }
                }
              }
            }
        ";
        $response = $this->client->post('https://api.github.com/graphql', [
            RequestOptions::HEADERS => [
                'Content-Type' => 'application/json',
            ],
            RequestOptions::BODY => json_encode(['query' => $query]),
        ], );

        $data = $response->getBody()->getContents();
        $data = json_decode($data);
        $result = [];

        foreach ($data->data->viewer->pullRequests->nodes as $row) {
            if (!in_array($row->baseRepository->nameWithOwner, $repositoryNames)) {
                continue;
            }

            $result[] = new PullRequestDto(
                number: $row->number,
                title: $row->title,
                url: $row->url,
                repositoryNameWithOwner: $row->baseRepository->nameWithOwner,
                mergeable: $row->mergeable,
            );
        }

        return $result;
    }
}
