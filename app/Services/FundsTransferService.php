<?php

namespace App\Services;

use App\Repositories\AccountNumberRepository;
use App\Repositories\CurrencyConversionRepository;
use App\Repositories\FundsTransferRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FundsTransferService
{
    protected FundsTransferRepository $fundsTransferRepository;
    protected CurrencyConversionRepository $currencyConversionRepository;
    protected AccountNumberRepository $accountNumberRepository;
    protected TransactionRepository $transactionRepository;
    public function __construct(
        FundsTransferRepository $fundsTransferRepository,
        CurrencyConversionRepository $currencyConversionRepository,
        AccountNumberRepository $accountNumberRepository,
        TransactionRepository $transactionRepository
    )
    {
        $this->fundsTransferRepository = $fundsTransferRepository;
        $this->currencyConversionRepository = $currencyConversionRepository;
        $this->accountNumberRepository = $accountNumberRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function sendFundsToUser($inputs, $sender): array
    {
        DB::beginTransaction();
        try {
            $senderAccount = $this->accountNumberRepository->findByUserId($sender->id);
            if(!$senderAccount){
                return [
                    'success' => false,
                    'errors' => ['account' => ['Sender account not found']],
                    'status' => 422
                ];
            }

            if($senderAccount->amount < $inputs['amount']){
                return [
                    'success' => false,
                    'errors' => ['account' => ['Insufficient balance']],
                    'status' => 422
                ];
            }

            // get Receiver Id with account number
            $receiverAccount = $this->accountNumberRepository->findByAccountNumber($inputs['account_number']);
            if(!$receiverAccount){
                Log::error('Receiver account not found: ' . $inputs['account_number']);
                return [
                    'success' => false,
                    'errors' => ['account' => ['Account number not found']],
                    'status' => 422
                ];
            }

            // Convert currency with API
            $convertedCurrency = $this->currencyConversionRepository->convertCurrency(
                $senderAccount->currency,
                $inputs['currency'],
                $inputs['amount'],
                $senderAccount->currency === $inputs['currency']
            );
            if(!$convertedCurrency['success']){
                Log::error('Currency conversion failed: ' . $convertedCurrency['error']);
                return [
                    'success' => false,
                    'errors' => ['currency' => ['Currency conversion failed']],
                    'status' => 422
                ];
            }

            $submitData = [
                'receiver_id' => $receiverAccount->user_id,
                'sender_id' => $sender->id,
                'amount' => $convertedCurrency['amount'],
                'currency' => $convertedCurrency['to_currency'],
                'description' => $inputs['description'],
            ];

            // Store funds transfer record
            $transferredFunds = $this->fundsTransferRepository->store($submitData);
            if(!$transferredFunds){
                DB::rollBack();
                return [
                    'success' => false,
                    'errors' => ['transfer' => ['Transfer failed']],
                    'status' => 422
                ];
            }

            // Update transaction records for sender
            $this->transactionRepository->storeTransaction([
                'user_id' => $sender->id,
                'fund_transfer_id' => $transferredFunds->id,
                'amount' => $inputs['amount'],
                'currency' => $senderAccount->currency,
                'type' => 'debit',
                'description' => $inputs['description'],
            ]);

            // Update transaction records for receiver
            $this->transactionRepository->storeTransaction([
                'user_id' => $receiverAccount->user_id,
                'fund_transfer_id' => $transferredFunds->id,
                'amount' => $convertedCurrency['amount'],
                'currency' => $convertedCurrency['to_currency'],
                'type' => 'credit',
                'description' => $inputs['description'],
            ]);

            // Update sender account balance
            $senderAccount->amount -= $inputs['amount'];
            $senderAccount->save();

            // Update receiver account balance
            $receiverAccount->amount += $inputs['amount'];
            $receiverAccount->save();

            DB::commit();

            return [
                'success' => true,
                'message' => 'Funds transferred successfully',
                'status' => 200
            ];

        }catch (\Exception $exception){
            Log::error('Error sending funds: ' . $exception->getMessage());
            DB::rollBack();
            return [
                'success' => false,
                'errors' => ['server_error' => ['An error occurred while sending funds.']],
                'status' => 500
            ];
        }
    }
}
