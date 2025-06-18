<?php

namespace App\Http\Controllers;

use App\Models\PaketVoucher;
use App\Models\Router;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PaketVoucherController extends Controller
{
    public function index($router_id)
    {
        try {
            $user = Auth::user();
            $router = Router::where('id', $router_id)->where('user_id', $user->id)->first();

            if (!$router) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Router not found or unauthorized.'
                ], 404);
            }

            $pakets = PaketVoucher::where('router_id', $router_id)->get();

            return response()->json([
                'status' => 'success',
                'data' => $pakets,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user vouchers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($router_id, $id)
    {
        try {
            $user = Auth::user();

            $paket = PaketVoucher::where('id', $id)->where('router_id', $router_id)->where('user_id', $user->id)->first();

            if (!$paket) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Voucher Packet not found or unauthorized.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $paket,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve voucher.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request, $router_id)
    {
        try {
            $user = Auth::user();

            $router = Router::where('id', $router_id)->where('user_id', $user->id)->first();
            
            if (!$router) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Router not found or unauthorized.'
                ], 404);
            }

            $validated = $request->validate([
                'voucher_name' => 'required|string|max:255',
                'address_pool' => 'nullable|string|max:255',
                'shared_user' => 'required|integer',
                'rate_limit' => 'required|string|max:255',
                'expired_mode' => 'required|string|max:255',
                'validity' => 'required|string|max:255',
                'price' => 'required|integer',
                'selling_price' => 'required|integer',
                'lock_user' => 'required|string|max:255',
                'parent_queue' => 'nullable|string|max:255',
            ]);

            $validated['user_id'] = $user->id;
            $validated['router_id'] = $router_id;

            $paket = PaketVoucher::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Voucher Packet created successfully.',
                'data' => $paket,
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
                'message' => 'Failed to create voucher packet.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $router_id, $id)
    {
        try {
            $user = Auth::user();
            $paket = PaketVoucher::where('id', $id)->where('router_id', $router_id)->where('user_id', $user->id)->first();

            if (!$paket) {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'Voucher Packet not found or unauthorized.'
                ], 404);
            }

            $validated = $request->validate([
                'voucher_name' => 'sometimes|required|string|max:255',
                'address_pool' => 'sometimes|nullable|string|max:255',
                'shared_user' => 'sometimes|required|integer',
                'rate_limit' => 'sometimes|required|string|max:255',
                'expired_mode' => 'sometimes|required|string|max:255',
                'validity' => 'sometimes|required|string|max:255',
                'price' => 'sometimes|required|integer',
                'selling_price' => 'sometimes|required|integer',
                'lock_user' => 'sometimes|required|string|max:255',
                'parent_queue' => 'sometimes|nullable|string|max:255',
                'router_id' => 'sometimes|required|exists:router,id',
            ]);

            $paket->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Voucher Packet updated successfully.',
                'data' => $paket,
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update voucher.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($router_id, $id)
    {
        try {
            $user = Auth::user();
            $paket = PaketVoucher::where('id', $id)->where('router_id', $router_id)->where('user_id', $user->id)->first();

            if (!$paket) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Voucher not found or unauthorized.',
                ], 404);
            }

            $paket->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Voucher Packet deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete voucher packet.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
