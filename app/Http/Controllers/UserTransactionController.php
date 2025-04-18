<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\UserTransaction;
use App\Repositories\TransactionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserTransactionController extends Controller
{
    protected TransactionRepository $transactionRepository;
    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function transactions($id): JsonResponse
    {
        try{
            $transactions = $this->transactionRepository
                ->getTransactions(['*'], ['fundTransfer:currency,id'])
                ->where('user_id', $id)
                ->latest()
                ->paginate(10);

            return response()->json([
                'success' => true,
                'transactions' => TransactionResource::collection($transactions)->response()->getData(true),
                'total' => $transactions->total(),
            ]);

        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['An error occurred while processing the request.']],
            ], 500);
        }
    }

    public function myTransactions(): JsonResponse
    {
        try{
            $transactions = $this->transactionRepository->getTransactions()
                ->where('user_id', Auth::user()->id)
                ->latest()->paginate(10);
            return response()->json([
                'success' => true,
                'transactions' => TransactionResource::collection($transactions)->response()->getData(true),
                'total' => $transactions->total(),
            ]);

        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['An error occurred while processing the request.']],
            ], 500);
        }
    }
}
