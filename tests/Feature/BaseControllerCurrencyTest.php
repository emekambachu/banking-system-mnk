<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Repositories\CurrencyConversionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;

class BaseControllerCurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_currencies_endpoint_returns_mocked_list(): void
    {
        // Fake the repository in the container
        $stub = new class {
            public function getCurrencyList(): array
            {
                return [
                    'success'    => true,
                    'currencies' => ['USD'=>1.0,'GBP'=>0.7,'EUR'=>0.9],
                    'status'     => 200
                ];
            }
        };
        App::instance(CurrencyConversionRepository::class, $stub);

        $response = $this->getJson('/currencies');

        $response
            ->assertStatus(200)
            ->assertJson([
                'success'    => true,
                'currencies' => ['USD'=>1.0,'GBP'=>0.7,'EUR'=>0.9],
                'status'     => 200
            ]);
    }

    public function test_currencies_endpoint_handles_exception(): void
    {
        // Stub that throws
        $stub = new class {
            public function getCurrencyList(): array
            {
                throw new \RuntimeException('API down');
            }
        };
        App::instance(CurrencyConversionRepository::class, $stub);

        $response = $this->getJson('/currencies');

        $response
            ->assertStatus(500)
            ->assertJson([
                'success' => false,
            ]);
    }
}
