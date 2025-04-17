<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;

class UserService
{
    protected UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function search($inputs): array
    {
        $searchResults = [];
        foreach ($inputs as $key => $input) {
            if($key === 'balance_greater_than' && !empty($input)){
                $searchResults[] = 'Balance greater than ' . $input;
            }
            if($key === 'balance_less_than' && !empty($input)){
                $searchResults[] = 'Balance less than ' . $input;
            }
            if($key === 'date_joined_before' && !empty($input)){
                $searchResults[] = 'Date joined before ' . $input;
            }
            if($key === 'date_joined_from' && !empty($input)){
                $searchResults[] = 'Date joined from ' . $input;
            }
            if($key === 'search_value' && !empty($input)){
                $searchResults[] = $input;
            }
        }

        $users = $this->userRepository->getUsers(
            ['users.*','users.id AS user_id', 'user_account_numbers.*', 'user_account_numbers.id AS account_number_id'],
            ['account', 'roles']

        )->leftjoin('user_account_numbers', 'user_account_numbers.user_id', '=', 'users.id')
            ->where(function($query) use ($inputs){
                $query->when(!empty($inputs['search_value']), static function($q) use($inputs){
                    $q->where('users.first_name', 'like' , '%'. $inputs['search_value'] .'%')
                        ->orWhere('users.last_name', 'like' , '%'. $inputs['search_value'] .'%')
                        ->orWhere('users.email', 'like' , '%'. $inputs['search_value'] .'%')
                        ->orWhere('users.mobile', 'like' , '%'. $inputs['search_value'] .'%')
                        ->orWhere('users.address', 'like' , '%'. $inputs['search_value'] .'%')
                        ->orWhere('user_account_numbers.account_number', 'like' , '%'. $inputs['search_value'] .'%');

                })->when(!empty($inputs['balance_greater_than']), static function ($query) use($inputs){
                    $query->where('user_account_numbers.amount', '>=', $inputs['balance_greater_than']);

                })->when(!empty($inputs['balance_less_than']), static function ($query) use($inputs){
                    $query->where('user_account_numbers.amount', '>=', $inputs['balance_less_than']);

                })->when(!empty($inputs['date_joined_before']), static function ($query) use($inputs){
                    $query->where('users.created_at', '>=', $inputs['date_joined_before']);

                })->when(!empty($inputs['date_joined_from']), static function ($query) use($inputs){
                    $query->where('users.created_at', '<=', $inputs['date_joined_from']);
                });

            })->distinct()->paginate(10);

        dd(UserResource::collection($users)->response()->getData(true));

        // if a result exists return results, else return empty array
        if($users->total() > 0){
            return [
                'success' => true,
                'users' => UserResource::collection($users)->response()->getData(true),
                'total' => $users->total(),
                'search_values' => $searchResults
            ];
        }

        return [
            'success' => true,
            'users' => [],
            'total' => 0,
            'search_values' => $searchResults
        ];
    }
}
