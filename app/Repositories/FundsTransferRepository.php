<?php

namespace App\Repositories;

use App\Models\FundTransfer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class FundsTransferRepository
{
    private FundTransfer $fundTransfer;
    public function __construct(){
        $this->fundTransfer = new FundTransfer();
    }

    public function getFundsTransfers($select = [], $relations = []): Builder|FundTransfer
    {
        $query = $this->fundTransfer;
        if(!empty($select)) {
            $query = $query->select($select);
        }
        if(!empty($relations)) {
            $query = $query->with($relations);
        }
        return $query;
    }

    public function store(array $storeData){
        return $this->fundTransfer->create([
            'sender_id' => $storeData['sender_id'],
            'receiver_id' => $storeData['receiver_id'],
            'amount' => $storeData['amount'],
            'currency' => $storeData['currency'],
            'description' => $storeData['description'],
        ]);
    }
}
