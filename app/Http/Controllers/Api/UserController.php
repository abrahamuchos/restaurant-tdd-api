<?php

namespace App\Http\Controllers\Api;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Store a newly created user in storage.
     *
     * @param StoreUserRequest $request
     *
     * @return JsonResponse
     * @throws \Throwable
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'last_name' => $request->lastName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            $user->assignRole(Roles::USER);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error(
                'Message: {message}  | File: {file} | Line: {line} | Database rollback transaction',
                [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            );
            return response()->json([
                'error' => true,
                'message' => 'Error while assigning role to user',
                'code' => 1037,
                'details' => 'Role not was assigned to user. User was deleted. Please, create user and a try again',
            ], 500);
        }

        return response()->json([], 201);
    }

    /**
     * @param UpdateUserRequest $request
     *
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request): JsonResponse
    {
        $wasUpdated = auth()->user()->update([
            'name' => $request->name,
            'last_name' => $request->lastName,
        ]);

        if ($wasUpdated) {
            return response()->json([], 204);
        } else {
            return response()->json([], 404);
        }
    }

    /**
     * Change password of authenticated user.
     *
     * @param UpdatePasswordRequest $request
     *
     * @return JsonResponse
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $wasUpdated = auth()->user()->update([
            'password' => Hash::make($request->input('newPassword')),
        ]);

        if ($wasUpdated) {
            return response()->json([], 204);
        } else {
            return response()->json([], 404);
        }
    }
}
