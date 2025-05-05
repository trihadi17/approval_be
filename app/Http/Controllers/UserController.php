<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

// Model
use App\Models\User;

// Request (Custom)
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    //* ADMIN
    // Data Semua User
    public function allUsers(){
        try {
            // Get
            $data = User::paginate(10);

            return response([
                'success' => true,
                'message' => 'Users Data',
                'data' => $data
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    // Detail User
    public function detailUser($id){
        try {
            // User
            $user = User::find($id);

            // Cek
            if (!$user) {
                return response([
                    'status' => false,
                    'message' => 'User data not found'
                ],404);
            }

            return response([
                'success' => true,
                'message' => 'User data by id',
                'data' => $user
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    // Mendaftarkan Verifikator
    public function verifierRegister(UserRequest $request){
        try {
            DB::beginTransaction();

            // credentials
            $credentials = [
                'id_card' => $request->id_card,
                'name' => $request->name,
                'email' => $request->email,
                'password' =>Hash::make($request->password),
                'address' => $request->address,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'profile_picture' => 'profile.jpg',
                'role' => 'Verifikator',
                'is_verified' => true,
                'verified_at' => now(),
            ];

            // create to datavase
            User::create($credentials);

            DB::commit();

            return response([
                'success' => true,
                'message' => 'Register Succesfull'
            ],200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ],500);
        }
    }

    // Mengubah Status Ordinary (user) sebagai verifikator
    public function changeRoles($id){
        try {
            DB::beginTransaction();

            // Get
            $user = User::where('id',$id)
                        ->where('role','Ordinary')
                        ->where('is_verified',true)
                        ->first();

            // Cek
            if (!$user) {
                return response([
                    'status' => false,
                    'message' => 'User data not found'
                ],404);
            }

            // Credentials
            $credentials = [
                'role' => 'Verifikator',
            ];

            // Update
            $user->update($credentials);

            DB::commit();

            return response([
                'success' => true,
                'message' => 'User successfully changed to verifier',
            ],200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    // Reset Password User
    public function passwordReset($id){
        try {
             DB::beginTransaction();

            // Get
            $user = User::find($id);

            // Cek
            if (!$user) {
                return response([
                    'status' => false,
                    'message' => 'User data not found'
                ],404);
            }

            // Credentials
            $credentials = [
                'password' => $user->id_card,
            ];

            // Update
            $user->update($credentials);

            DB::commit();

            return response([
                'success' => true,
                'message' => 'User password reset successful',
            ],200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    //* VERIFIKATOR
    public function userStatus(Request $request){
        try {
            // Validasi Input
            $request->validate([
                'status' => 'nullable|boolean'
            ]);

            // Inisialisasi data tanpa mengambil langsung data dari DB
            $user = User::query();

            // Filter Status
            if ($request->filled('status')) {
                // Data berdasarkan status
               $user->where('role','Ordinary')
               ->where('is_verified',$request->status);
            }
            
            // Menampilkan data (pagination)
            $data = $user->where('role','Ordinary')->paginate(10);

            return response([
                'success' => true,
                'message' => 'User Data',
                'data' => $data
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ],500);
        }
    }

    // Verifikasi Pendaftaran Pengguna
    public function userVerification($id){
       try {
            DB::beginTransaction();

            // Get
            $user = User::where('id',$id)
                        ->where('role','Ordinary')
                        ->where('is_verified',false)
                        ->first();

            // Cek
            if (!$user) {
                return response([
                    'status' => false,
                    'message' => 'User data not found'
                ],404);
            }

            // Credentials
            $credentials = [
                'is_verified' => true,
                'verified_at' => now()
            ];

            // Update
            $user->update($credentials);

            DB::commit();

            return response([
                'success' => true,
                'message' => 'The user has been successfully verified',
            ],200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    
    //* ORDINARY (USER)
    // Mendaftar Akun
    public function register(UserRequest $request){
        try {
            DB::beginTransaction();

            // credentials
            $credentials = [
                'id_card' => $request->id_card,
                'name' => $request->name,
                'email' => $request->email,
                'password' =>Hash::make($request->password),
                'address' => $request->address,
                'gender' => $request->gender,
                'phone_number' => $request->phone_number,
                'profile_picture' => 'profile.jpg',
                'role' => 'Ordinary',
                'is_verified' => false,
            ];

            // create to database
            User::create($credentials);

            DB::commit();

            return response([
                'success' => true,
                'message' => 'Register Succesfull'
            ],200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ],500);
        }
    }

    // Mengubah Password
    public function passwordUpdate(Request $request){
        try {
            DB::beginTransaction();

            // Validasi Input
            $request->validate([
                'current_password' => 'required|min:8',
                'password' => 'required|string|min:8|confirmed'
            ]);

            // Current User
            $user = JWTAuth::user();

            if (!Hash::check($request->current_password,$user->password)) {
                return response([
                    'status' => false,
                    'message' => 'The old password doesnt match'
                ],400);
            }

            // Credentials
            $credentials = [
                'password' => Hash::make($request->password)
            ];

            // Update
            $user->update($credentials);

            DB::commit();

            return response([
                'success' => true,
                'message' => 'Password changed successfully'
            ],200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }

    }
}

    
