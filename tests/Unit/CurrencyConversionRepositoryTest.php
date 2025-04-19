<?php

namespace Tests\Unit;

use App\Repositories\CurrencyConversionRepository;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use ReflectionClass;
use Tests\TestCase;

class CurrencyConversionRepositoryTest extends TestCase
{
    /**
     * @throws \JsonException
     */
    public function test_get_currency_list_success(): void
    {
        // Prepare fake API response payload
        $payload = json_encode([
            'rates' => ['USD' => 1.0, 'GBP' => 0.8, 'EUR' => 0.9],
            'base' => 'USD',
            'date' => '2025-04-18'
        ], JSON_THROW_ON_ERROR);

        // Mock Guzzle client to return our payload
        $mock    = new MockHandler([new Response(200, [], $payload)]);
        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $repo = new CurrencyConversionRepository();

        // Override private properties via Reflection
        $ref = new ReflectionClass($repo);
        $propClient = $ref->getProperty('client');
        $propClient->setValue($repo, $client);

        $propKey = $ref->getProperty('accessKey');
        $propKey->setValue($repo, 'dummykey');

        $result = $repo->getCurrencyList();

        $this->assertTrue($result['success']);
        $this->assertEquals(200, $result['status']);
        $this->assertArrayHasKey('currencies', $result);
        $this->assertEquals('USD', $result['currencies']['base']);
    }

    public function test_get_currency_list_failure(): void
    {
        // Mock Guzzle client to throw exception
        $mock    = new MockHandler([new \Exception('Network error')]);
        $handler = HandlerStack::create($mock);
        $client  = new Client(['handler' => $handler]);

        $repo = new CurrencyConversionRepository();
        $ref = new ReflectionClass($repo);
        $ref->getProperty('client')->setValue($repo, $client);
        $ref->getProperty('accessKey')->setValue($repo, 'dummykey');

        $result = $repo->getCurrencyList();

        $this->assertFalse($result['success']);
        $this->assertEquals(500, $result['status']);
        $this->assertArrayHasKey('errors', $result);
    }
}
