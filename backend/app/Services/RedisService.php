<?php

namespace App\Services;

use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;

/**
 * RedisService is used for easier work with Redis database.
 *
 * It's registered as a singleton object in AppServiceProvider.
 */
class RedisService
{
    private const EXPIRE_RESOLUTION = 'EX'; // EX - seconds

    private Connection $connection;

    public function __construct()
    {
        $this->connection = Redis::connection();
    }

    /**
     * Returns value that is stored inside Redis by passed key
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        $value = $this->connection->get($key);

        return $value ? json_decode($value, true) : null;
    }

    /**
     * Sets a passed value to the Redis database under the passed key
     *
     * @param string $key
     * @param mixed $value
     * @param int $ttl seconds
     * @return $this
     */
    public function set(string $key, mixed $value, int $ttl): self
    {
        $this->connection->set($key, json_encode($value), self::EXPIRE_RESOLUTION, $ttl);

        return $this;
    }

    /**
     * If value under the passed key is stored inside Redis database, then it will be returned.
     * If not, then value returned in callback will be saved to the Redis database and returned.
     *
     * @param string $key
     * @param callable $callback
     * @param int $ttl
     * @return mixed
     */
    public function getOrSet(string $key, callable $callback, int $ttl): mixed
    {
        $cached = $this->get($key);

        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        $this->set($key, $value, $ttl);

        return $value;
    }

}
