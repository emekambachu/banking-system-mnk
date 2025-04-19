<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class UserRepository
{
    private User $user;
    public function __construct()
    {
        $this->user = new User();
    }

    public function user(): User
    {
        return $this->user;
    }

    public function storeUser(array $data)
    {
        return $this->user->create($data);
    }

    public function getUsers($select = [], $relations = []): Builder|User
    {
        $query = $this->user;

        if(!empty($select)) {
            $query = $query->select($select);
        }

        if(!empty($relations)) {
            $query = $query->with($relations);
        }

        return $query;
    }

    public function getUserById($id, $relations = [], $select = []): Model|Collection|Builder|array|null
    {
        $query = $this->user;
        if(!empty($relations)) {
            $query = $query->with($relations);
        }

        if(!empty($select)) {
            $query = $query->select($select);
        }

        return $query->findOrFail($id);
    }

    public function getUserByEmail($email, $relations = [], $select = []): Model|Collection|Builder|array|null
    {
        $query = $this->user;
        if(!empty($relations)) {
            $query = $query->with($relations);
        }

        if(!empty($select)) {
            $query = $query->select($select);
        }

        return $query->where('email', $email)->first();
    }

    public function getUserByAccountNumber($accountNumber, $relations = [], $select = []): Model|Collection|Builder|array|null
    {
        $query = $this->user;
        if(!empty($relations)) {
            $query = $query->with($relations);
        }

        if(!empty($select)) {
            $query = $query->select($select);
        }

        return $query->whereHas('account', function ($query) use ($accountNumber) {
            $query->where('account_number', $accountNumber);
        })->first();
    }

    public function isVerified($userId): bool
    {
        $user = $this->getUserById($userId, [], ['id', 'status']);
        return $user?->status === 1;
    }

    public function verifyUser($userId)
    {
        $user = $this->getUserById($userId, [], ['id', 'status']);
        if ($user->status === 1) {
            $user->status = 0;
        }else{
            $user->status = 1;
        }
        return $user->save();
    }
}
