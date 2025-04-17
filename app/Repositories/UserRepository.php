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

    public function updateUser($id, array $data): bool
    {
        return $this->user->where('id', $id)->update($data);
    }

    public function deleteUser($id, $relations = []): void
    {
        $module = $this->user->with($relations)->find($id);

        if (count($relations) > 0) {
            foreach ($relations as $relation) {
                $module->$relation()->delete();
            }
        }

        $module->delete();
    }
}
