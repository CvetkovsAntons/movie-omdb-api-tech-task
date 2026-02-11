<?php

namespace Tests\Unit;

use App\Services\RedisService;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;
use Mockery;
use Tests\TestCase;

final class RedisServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_returns_null_when_key_missing(): void
    {
        $conn = Mockery::mock(Connection::class);

        $conn
            ->shouldReceive('get')
            ->once()
            ->with('k1')
            ->andReturn(null);

        Redis::shouldReceive('connection')
            ->once()
            ->andReturn($conn);

        $service = new RedisService();

        $this->assertNull($service->get('k1'));
    }

    public function test_get_decodes_json_value(): void
    {
        $payload = ['a' => 1, 'b' => 'x'];

        $conn = Mockery::mock(Connection::class);

        $conn
            ->shouldReceive('get')
            ->once()
            ->with('k1')
            ->andReturn(json_encode($payload));

        Redis::shouldReceive('connection')
            ->once()
            ->andReturn($conn);

        $service = new RedisService();

        $this->assertSame($payload, $service->get('k1'));
    }

    public function test_set_encodes_value_and_sets_ttl(): void
    {
        $payload = ['ok' => true];

        $conn = Mockery::mock(Connection::class);

        $conn
            ->shouldReceive('set')
            ->once()
            ->with('k1', json_encode($payload), 'EX', 60)
            ->andReturnTrue();

        Redis::shouldReceive('connection')
            ->once()
            ->andReturn($conn);

        $service = new RedisService();

        $returned = $service->set('k1', $payload, 60);

        $this->assertInstanceOf(RedisService::class, $returned);
    }

    public function test_get_or_set_returns_cached_value_and_does_not_call_callback(): void
    {
        $cached = ['x' => 123];

        $conn = Mockery::mock(Connection::class);

        $conn
            ->shouldReceive('get')
            ->once()
            ->with('k1')
            ->andReturn(json_encode($cached));

        $conn->shouldNotReceive('set');

        Redis::shouldReceive('connection')
            ->once()
            ->andReturn($conn);

        $service = new RedisService();

        $called = false;

        $result = $service->getOrSet(
            key: 'k1',
            callback: function () use (&$called) {
                $called = true;
                return ['y' => 999];
            },
            ttl: 60,
        );

        $this->assertFalse($called);
        $this->assertSame($cached, $result);
    }

    public function test_get_or_set_calls_callback_and_caches_when_missing(): void
    {
        $value = ['movie' => 'Matrix'];

        $conn = Mockery::mock(Connection::class);

        $conn
            ->shouldReceive('get')
            ->once()
            ->with('k1')
            ->andReturn(null);

        $conn
            ->shouldReceive('set')
            ->once()
            ->with('k1', json_encode($value), 'EX', 120)
            ->andReturnTrue();

        Redis::shouldReceive('connection')
            ->once()
            ->andReturn($conn);

        $service = new RedisService();

        $result = $service->getOrSet('k1', fn () => $value, 120);

        $this->assertSame($value, $result);
    }

}
