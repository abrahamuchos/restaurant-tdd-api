<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
           'email' => 'required|email',
           'password' => 'required|string|max:65'
        ]);

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->_respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return JsonResponse
     */
    private function _respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'data' => [
                'token' => $token,
                'tokenType' => 'bearer',
                'expiresIn' => auth()->factory()->getTTL() * 60
            ]
        ]);
    }
}
