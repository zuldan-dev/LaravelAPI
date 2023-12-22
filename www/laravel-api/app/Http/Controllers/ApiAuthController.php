<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ApiAuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        try {
            if (Auth::attempt($credentials)) {
                $token = $request->user()->createToken('api-token')->plainTextToken;

                return response()->json(['token' => $token]);
            } else {
                return response()->json(['error' => User::LOGIN_ERROR_MESSAGE], Response::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens->each(function ($token, $key) {
            $token->delete();
        });

        return response()->json(['message' => User::LOGOUT_MESSAGE]);
    }
}
