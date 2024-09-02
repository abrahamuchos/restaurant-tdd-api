<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|max:65'
        ]);

        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->_respondWithToken($token);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function sendingResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|exists:users,email',
        ]);

        /* We will send the password reset link to this user. Once we have this email we will look
         up the user that has that email and send them a link to reset their password. If the email exists
         in our database we will send the user a password reset link
        */
        $status = Password::sendResetLink(
            $request->only('email')
        );


        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'ok']);

        } else {
            return response()->json([
                'error' => true,
                'code' => 5001,
                'message' => 'Unable to send reset password link',
                'details' => 'We are unable to find a user with that email address.',
            ], 500);
        }

    }

    /**
     * @param ResetPasswordRequest $request
     *
     * @return JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'), // $credentials
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(\Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );


        if($status === Password::INVALID_USER) {
            return response()->json([
                'error' => true,
                'code' => 5002,
                'message' => 'Unable to change password',
                'details' => 'We are unable to find a user with that email address.',
            ], 404);

        }else if($status === Password::INVALID_TOKEN){
            return response()->json([
                'error' => true,
                'code' => 5003,
                'message' => 'Unable to change password',
                'details' => 'We are unable to find token, please try again',
            ], 403);

        }else {
            return response()->json([], 204);
        }
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
