<?php

namespace App\Http\Controllers;

use App\Models\UserList;
use App\Models\Router;
use App\Models\PaketVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class UserListController extends Controller
{
    public function index($router_id)
    {
        try {
            $user = Auth::user();

            // Pastikan router memang milik user login
            $router = Router::where('id', $router_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$router) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Router not found or unauthorized.',
                ], 404);
            }

            // Ambil semua paket voucher yang terhubung ke router ini
            $paketIds = PaketVoucher::where('router_id', $router_id);

            // Ambil user list berdasarkan paket_voucher_id
            $userLists = UserList::whereIn('paket_voucher_id', $paketIds)->get();
            
            // dd($userLists);

            return response()->json([
                'status' => 'success',
                'data' => $userLists,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user list.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request, $router_id)
    {
        try {
            $user = Auth::user();

            // Pastikan router memang milik user login
            $router = Router::where('id', $router_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$router) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Router not found or unauthorized.',
                ], 404);
            }

            // Pastikan paket voucher milik router dan user yang benar
            $validated = $request->validate([
                'server' => 'required|string|max:255',
                'user' => 'required|string|max:255',
                'user_password' => 'nullable|string|max:255',
                'profile' => 'required|string|max:255',
                'time_limit' => 'nullable|integer',
                'data_limit' => 'nullable|integer',
                'comment' => 'nullable|string',
                'paket_voucher_id' => 'required|exists:paket_voucher,id',
            ]);

            // Pastikan paket_voucher memang milik router
            $paket = PaketVoucher::where('id', $validated['paket_voucher_id'])
                ->where('router_id', $router_id);

            if (!$paket) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Paket Voucher or unauthorized.',
                ], 403);
            }

            $validated['user_id'] = $user->id;

            $userList = UserList::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'User List created successfully.',
                'data' => $userList,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create User List.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function generateUsers()
    {

    }

    public function update(Request $request, $router_id, $id)
    {
        try {
            $user = Auth::user();

            $userList = UserList::with('paketVoucher')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->whereHas('paketVoucher', function ($q) use ($router_id) {
                    $q->where('router_id', $router_id);
                })
                ->first();

            if (!$userList) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User List not found or unauthorized for this router.',
                ], 404);
            }

            $validated = $request->validate([
                'user' => 'sometimes|required|string|max:255',
                'user_password' => 'sometimes|nullable|string|max:255',
                'profile' => 'sometimes|required|string|max:255',
                'time_limit' => 'sometimes|nullable|integer',
                'data_limit' => 'sometimes|nullable|integer',
                'comment' => 'sometimes|nullable|string',
                'paket_voucher_id' => 'sometimes|required|exists:paket_voucher,id',
            ]);

            // Jika user mengganti paket_voucher, validasi ulang kepemilikan
            if (isset($validated['paket_voucher_id'])) {
                $paket = PaketVoucher::where('id', $validated['paket_voucher_id'])
                    ->where('router_id', $router_id)
                    ->where('user_id', $user->id)
                    ->first();

                if (!$paket) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid Paket Voucher or unauthorized.',
                    ], 403);
                }
            }

            $userList->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'User List updated successfully.',
                'data' => $userList,
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update User List.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function destroy($router_id, $id)
    {
        try {
            $user = Auth::user();

            $userList = UserList::where('id', $id)->where('user_id', $user->id)->first();

            if (!$userList) {
                return response()->json(['status' => 'error', 'message' => 'User not found or unauthorized.'], 404);
            }

            $userList->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
