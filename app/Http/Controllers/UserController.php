<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function index(): JsonResponse
    {
        $users = $this->userRepository->getUsers(
            ['id', 'first_name', 'last_name', 'mobile', 'email', 'address', 'created_at'],
            ['account_number', 'roles']
        )->orderBy('last_name', 'asc')->paginate(10);

        return response()->json([
            'success' => true,
            'users' => UserResource::collection($users)->response()->getData(true),
        ]);
    }
}
