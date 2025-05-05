<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
// Model
use App\Models\Permission;

class PermissionController extends Controller
{
    //* ADMIN
    // Melihat Daftar Izin Yang Diajukan
    public function permissions(){
        try {
            // Get
            $data = Permission::with('user')->paginate(10);

            return response([
                'success' => true,
                'message' => 'Permissions Data',
                'data' => $data
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

     // Detail Permission
    public function detailPermission($id){
        try {
            // Permission
            $permission = Permission::with('user')->find($id);

            // Cek
            if (!$permission) {
                return response([
                    'status' => false,
                    'message' => 'Permission data not found'
                ],404);
            }

            return response([
                'success' => true,
                'message' => 'Permission data by id',
                'data' => $permission
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    //* VERIFIKATOR
    // Melihat Daftar Izin Dengan Filter (Status)
    public function permissionStatus(Request $request){
        try {
            // Validasi Input
            $request->validate([
                'status' => 'nullable|in:Approved,Rejected,Revised,Pending,Canceled'
            ]);

            // Inisialisasi data tanpa mengambil langsung data dari DB
            $permission = Permission::query();

            // Filter Status
            if ($request->filled('status')) {
                // Data berdasarkan status
               $permission->where('status',$request->status);
            }
            
            // Menampilkan data (pagination)
            $data = $permission->with('user')->paginate(10);

            return response([
                'success' => true,
                'message' => 'Permission Data',
                'data' => $data
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ],500);
        }
    }

    // Melakukan ACC, Revisi, Penolakan Pengajuan Izin
    public function decision(Request $request, $id){
        try {
            DB::beginTransaction();

            // Validasi Input
            $request->validate([
                'status' => 'required|in:Approved,Rejected,Revised',
                'note' => 'required'
            ]);

            // Get
            $permission = Permission::where('id',$id)->where('status', '!=', 'Approved')
            ->where('status','!=','Rejected')
            ->first();

            // Cek
            if (!$permission) {
                return response([
                    'status' => false,
                    'message' => 'Permission data not found'
                ],404);
            }

            // Credentials
            $credentials = [
                'status' => $request->status,
                'note' => $request->note
            ];

            // Update
            $permission->update($credentials);

            DB::commit();

            return response([
                'success' => true,
                'message' => 'The decision has been changed',
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
    public function applyPermission(PermissionRequest $request){
        try {
            DB::beginTransaction();

            // Credentials
            $credentials = [
                'user_id' => JWTAuth::user()->id,
                'type' => $request->type,
                'reason' => $request->reason,
                'status' => 'Pending'
            ];

            // create to database
            Permission::create($credentials);

            DB::commit();

            return response([
                'success' => true,
                'message' => 'Successfully submitted a permit application'
            ],200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    // Melihat Daftar Izin
    public function permissionList(){
        try {
            // Get
            $data = Permission::where('user_id',JWTAuth::user()->id)->paginate(10);

            return response([
                'success' => true,
                'message' => 'Permissions Data',
                'data' => $data
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    // Mengubah Detail Izin Jika Status Masih Pending/Revisi
    public function changePermission(PermissionRequest $request,$id){
        try {

            DB::beginTransaction();

            // Get
            $permission = Permission::where('id',$id)
                                    ->where('user_id',JWTAuth::user()->id)
                                    ->where('status','!=','Approved')
                                    ->where('status','!=','Rejected')
                                    ->first();
            
            // Cek
            if (!$permission) {
                return response([
                    'status' => false,
                    'message' => 'Permission data not found'
                ],404);
            }

            // Credentials
            $credentials = [
                'type' => $request->type,
                'reason' => $request->reason,
            ];

            // Update
            $permission->update($credentials);

            DB::commit();

            return response([
                'success' => true,
                'message' => 'Permission data changed successfully',
            ],200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    // Melihat Status Pengajuan (Detail Pengajuan)
    public function permissionStatusDetail($id){
        try {
            // Permission
            $permission = Permission::where('id',$id)
                                    ->where('user_id',JWTAuth::user()->id)
                                    ->first();

            // Cek
            if (!$permission) {
                return response([
                    'status' => false,
                    'message' => 'Permission data not found'
                ],404);
            }

            return response([
                'success' => true,
                'message' => 'Permission status detail',
                'data' => $permission
            ],200);
        } catch (\Throwable $e) {
            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    // Membatalkan pengajuan Izin (Dengan syarat masih pending/revisi)
    public function cancelPermission($id){
          try {

            DB::beginTransaction();

            // Get
            $permission = Permission::where('id',$id)
                                    ->where('user_id',JWTAuth::user()->id)
                                    ->where('status','!=','Approved')
                                    ->where('status','!=','Rejected')
                                    ->where('status','!=','Canceled')
                                    ->first();
            
            // Cek
            if (!$permission) {
                return response([
                    'status' => false,
                    'message' => 'Permission data not found'
                ],404);
            }

            // Credentials
            $credentials = [
                'status' => 'Canceled'
            ];

            // Update
            $permission->update($credentials);

            DB::commit();

            return response([
                'success' => true,
                'message' => 'Permission data successfully cancelled',
            ],200);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response([
                'status' => false,
                'message' => 'An error occurred, please try again.' . $e
            ], 500);
        }
    }

    // Menghapus Pengajuan Izin (Dengan syarat masih pending/canceled)
    public function removePermission($id){
         try {

            DB::beginTransaction();

            // Get
            $permission = Permission::where('id',$id)
                                    ->where('user_id',JWTAuth::user()->id)
                                    ->where('status','!=','Approved')
                                    ->where('status','!=','Rejected')
                                    ->where('status','!=','Revised')
                                    ->first();
            
            // Cek
            if (!$permission) {
                return response([
                    'status' => false,
                    'message' => 'Permission data not found'
                ],404);
            }

            // Delete
            $permission->delete();

            DB::commit();

            return response([
                'success' => true,
                'message' => 'Permission data successfully deleted',
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
