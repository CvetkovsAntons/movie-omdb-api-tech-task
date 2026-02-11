<?php

namespace App\Http\Controllers;

use App\Services\External\OMDbApi\OMDbApiClient;
use App\Services\RedisService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class MovieApiController extends Controller
{
    public function __construct(
        private readonly OMDbApiClient $client,
        private readonly RedisService  $redis,
    ) {}

    /**
     * Processes GET: /api/movies/search request. Returns list of found movies.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ConnectionException
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate(['s' => ['required', 'string', 'min:2']]);

        $searchQuery = $request->query('s');

        $result = $this->redis->getOrSet(
            key: sprintf('api:movies:search:%s', $searchQuery),
            callback: fn () => $this->client->search($searchQuery),
            ttl: 60,
        );

        return response()->json([
            'results' => $result['Search'] ?? [],
            'total'   => $result['totalResults'] ?? 0,
        ]);
    }

    /**
     * Processes GET: /api/movie/{id} request. Returns found movie by imdb ID.
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     * @throws ConnectionException
     */
    public function getById(Request $request, string $id): JsonResponse
    {
        $result = $this->redis->getOrSet(
            key: sprintf('api:movies:id:%s', $id),
            callback: fn () => $this->client->getById($id),
            ttl: 60 * 5,
        );

        return response()->json($result);
    }

}
