<?php

namespace App\Http\Controllers;

use App\Http\Requests\FundTransferRequest;
use App\Repositories\AccountNumberRepository;
use App\Services\FundsTransferService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserFundsTransferController extends Controller
{
    protected FundsTransferService $fundsTransferService;
    protected AccountNumberRepository $accountNumberRepository;
    public function __construct(
        FundsTransferService $fundsTransferService,
        AccountNumberRepository $accountNumberRepository
    )
    {
        $this->fundsTransferService = $fundsTransferService;
        $this->accountNumberRepository = $accountNumberRepository;
    }

    public function sendFunds(FundTransferRequest $request): JsonResponse
    {
        try {
            $response = $this->fundsTransferService->sendFundsToUser($request->all(), Auth::user());
            return response()->json($response, $response['status']);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['An error occurred while processing the request.']],
            ], 500);
        }
    }

    public function getBeneficiary(Request $request): JsonResponse
    {
        try {
            $account = $this->accountNumberRepository->findByAccountNumber($request->account_number);

            if(!$account){
                return response()->json([
                    'success' => false,
                    'errors' => ['account' => ['Beneficiary Not Found']],
                    'status' => 422
                ]);
            }
            return response()->json([
                'success' => true,
                'beneficiary' => [
                    'id' => $account->user->id,
                    'first_name' => $account->user->first_name,
                    'last_name' => $account->user->last_name,
                    'account_number' => $account->account_number,
                    'currency' => $account->currency,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['An error occurred while processing the request.']],
            ], 500);
        }
    }

}
