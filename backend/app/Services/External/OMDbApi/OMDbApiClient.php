<?php

namespace App\Services\External\OMDbApi;

use App\Services\External\OMDbApi\Exceptions\OMDbApiException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

/**
 * OBDbApiClient is used to make API calls to the external OMDb Api.
 *
 * Its declared as a singleton object in AppServiceProvider.
 */
readonly class OMDbApiClient
{
    public function __construct(
        private string $baseUrl,
        private string $token,
        private int    $timeout,
    ) {}

    /**
     * Prepares base of the OBDb Api request.
     *
     * @return PendingRequest
     */
    private function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->withQueryParameters(['apiKey' => $this->token])
            ->acceptJson()
            ->timeout($this->timeout);
    }

    /**
     * Processes movie search request.
     *
     * @param string $searchQuery
     * @return array
     * @throws ConnectionException
     */
    public function search(string $searchQuery): array
    {
        $response = $this->http()->get('', ['s' => $searchQuery]);

        if ($response->failed()) {
            throw new OMDbApiException($response->json()['Error'] ?? 'Movie search request failed');
        }

        return $response->json();
    }

    /**
     * Returns movie from OMDb Api by its imdb ID
     *
     * @param string $id
     * @return array
     * @throws ConnectionException
     */
    public function getById(string $id): array
    {
        $response = $this->http()->get('', ['i' => $id]);

        if ($response->failed()) {
            throw new OMDbApiException($response->json()['Error'] ?? "Couldn't get the movie by title");
        }

        return $response->json();
    }

}
