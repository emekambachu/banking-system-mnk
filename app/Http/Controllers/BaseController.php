<?php

namespace App\Http\Controllers;

use App\Repositories\CurrencyConversionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{
    protected CurrencyConversionRepository $currencyConversionRepository;
    public function __construct(CurrencyConversionRepository $currencyConversionRepository){
        $this->currencyConversionRepository = $currencyConversionRepository;
    }

    public function currencies(): JsonResponse
    {
        try {
            $response = $this->currencyConversionRepository->getCurrencyList();
            return response()->json($response, $response['status']);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['An error occurred while fetching currency conversion.']],
            ], 500);
        }
    }

}
