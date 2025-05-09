<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUsersRequest;
use App\Http\Requests\UserRegisterRequest;
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
                ['id', 'first_name', 'last_name', 'mobile', 'email', 'address', 'created_at', 'status'],
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

    public function store(StoreUsersRequest $request): JsonResponse
    {
        try {
            $response = $this->userService->storeUsers($request->all());
            return response()->json($response, $response['status']);

        }catch (\Exception $exception){

            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['Unexpected error occurred']],
            ], 500);
        }
    }

    public function updateStatus($id): JsonResponse
    {
        try {
            $user = $this->userRepository->verifyUser($id);
            if(!$user) {
                return response()->json([
                    'success' => false,
                    'errors' => ['user_not_found' => ['User not found']],
                    'user' => null,
                ], 404);
            }
            return response()->json([
                'success' => true,
                'user' => new UserResource($user),
            ]);

        }catch (\Exception $exception){

            Log::error($exception->getMessage());
            return response()->json([
                'success' => false,
                'errors' => ['server_error' => ['Unexpected error occurred']],
            ], 500);
        }
    }


}
