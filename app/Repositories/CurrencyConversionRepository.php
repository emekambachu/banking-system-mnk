<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CurrencyConversionRepository
{
    private mixed $accessKey;
    private Client $client;
    public function __construct(){
        $this->accessKey = config('services.exchange_rates.key');
        $this->client = new Client([
            'verify' => false
        ]);
    }

    private function getApiUrl($endpoint, $params = []): string
    {
        $url = "https://api.exchangeratesapi.io/v1/{$endpoint}?access_key={$this->accessKey}";
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        return $url;
    }

    private function checkAccessKey(): array|null
    {
        if(!$this->accessKey || !isset($this->accessKey)){
            Log::error('Access key is not set or empty');
            return [
                'success' => false,
                'errors' => ['server_error' => ['Access key is not set or empty']],
                'status' => 422
            ];
        }
        return null;
    }

    public function getCurrencyList(): array
    {
        $this->checkAccessKey();
        $apiUrl = "https://api.exchangeratesapi.io/v1/latest?access_key={$this->accessKey}&symbols=USD,GBP,EUR&base=USD";

        try {
            $response = $this->client->get($apiUrl);
            $currencies = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            return [
                'success' => true,
                'currencies' => $currencies,
                'status' => 200
            ];

        } catch (\Exception $e) {
            Log::error($e->getMessage());
           return [
                'success' => false,
                'errors' => ['server_error' => ['An error occurred while fetching currency list.']],
                'status' => 500
            ];
        }
    }

    public function convertCurrency($from, $to, $amount): array
    {
        $this->checkAccessKey();
        $apiUrl = "https://api.exchangeratesapi.io/v1/convert?access_key={$this->accessKey}&from={$from}&to={$to}&amount={$amount}";

        try {
            $response = $this->client->get($apiUrl);
            $conversion = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
            return [
                'success' => true,
                'conversion' => $conversion,
                'status' => 200
            ];

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return [
                'success' => false,
                'errors' => ['server_error' => ['An error occurred while converting currency.']],
                'status' => 500
            ];
        }
    }


}
