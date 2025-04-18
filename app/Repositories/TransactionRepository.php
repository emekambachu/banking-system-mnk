<?php

namespace App\Repositories;

use App\Models\UserTransaction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class TransactionRepository
{
    private UserTransaction $transaction;
    public function __construct(){
        $this->transaction = new UserTransaction();
    }

    public function getTransactions($select = [], $relations = []): Builder|UserTransaction
    {
        $query = $this->transaction;

        if(!empty($select)) {
            $query = $query->select($select);
        }

        if(!empty($relations)) {
            $query = $query->with($relations);
        }

        return $query;
    }

    public function getTransactionsByUserId($id, $relations = [], $select = []): Model|Collection|Builder|array|null
    {
        $query = $this->transaction;
        if(!empty($relations)) {
            $query = $query->with($relations);
        }

        if(!empty($select)) {
            $query = $query->select($select);
        }

        return $query->where('user_id', $id);
    }

    public function storeTransaction($storeData){
        return $this->transaction->create([
            'user_id' => $storeData['user_id'],
            'fund_transfer_id' => $storeData['fund_transfer_id'],
            'amount' => $storeData['amount'],
            'currency' => $storeData['currency'],
            'type' => $storeData['type'],
            'description' => $storeData['description'],
        ]);
    }
}
