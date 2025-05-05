<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

// Request
use App\Http\Requests\AuthRequest;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\Auth;
// Library (JWT)
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    // AUTH (Login, Current User, Logout)
    public function login(AuthRequest $request){
        try {
            // Invalid Credentials
            if (!$token = JWTAuth::attempt($request->only('email','password'))) {
                return response([
                    'status' => false,
                    'message' => 'Invalid credentials'
                ],401);
            }

            // Current user
            $user = JWTAuth::user();

            // Cek udah terverifikasi/belum
            if (!$user->is_verified) {
                return response([
                    'status' => false,
                    'message' => 'Account not verified, please wait'
                ],403);
            }

            return response([
                'success' => true,
                'message' => 'Login successfull',
                'data' => [
                    'user' => $user,
                    'token_type' => 'bearer',
                    'token' => $token,
                    'expires_in' => JWTAuth::factory()->getTTL() * 60,
                ]
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    public function currentUser(){
         try {
            // Current user
            $user = JWTAuth::user();

            return response([
                'success' => true,
                'message' => 'Login successfull',
                'user' => $user,
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'Could not retrieve user'
            ], 500);
        }
    }

    public function logout(){
        try {
            // Membatalkan token yang sedang aktif
            JWTAuth::invalidate(JWTAuth::getToken());

            return response([
                'success' => true,
                'message' => 'Logged out successfully'
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }
}
