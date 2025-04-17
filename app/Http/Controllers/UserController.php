<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Repositories\UserRepository;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected UserRepository $userRepository;
    protected UserService $userService;
    public function __construct(
        UserRepository $userRepository,
        UserService $userService
    )
    {
        $this->userRepository = $userRepository;
        $this->userService = $userService;
    }

    public function index(): JsonResponse
    {
        try{

            $users = $this->userRepository->getUsers(
                ['id', 'first_name', 'last_name', 'mobile', 'email', 'address', 'created_at'],
                ['account', 'roles']
            )->orderBy('last_name', 'asc')->paginate(10);

            return response()->json([
                'success' => true,
                'users' => UserResource::collection($users)->response()->getData(true),
            ]);

        }catch (\Exception $e){

            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['An error occurred while fetching users.']],
            ], 500);

        }
    }

    public function search(Request $request): JsonResponse
    {
        try {
            $response = $this->userService->search($request->except('page'));
            return response()->json($response);

        }catch (\Exception $e){
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['An error occurred while searching users.']],
            ], 500);
        }
    }
}
