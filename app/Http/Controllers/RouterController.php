<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\Router;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;


class RouterController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized',
                ], 401);
            }

            $routers = $user->routers;

            return response()->json([
                'status' => 'success',
                'data' => $routers,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch user routers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = Auth::user();

            $router = Router::where('id', $id)
                            ->where('user_id', $user->id)
                            ->first();

            if (!$router) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Router not found or unauthorized.',
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'data' => $router,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve router details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validated = $request->validate([
                'session_name' => 'required|string|max:255',
                'ip_mikrotik' => 'required|ip',
                'user_mikrotik' => 'required|string|max:255',
                'password_mikrotik' => 'required|min:6|string|max:255',
                'hostpot_name' => 'required|string|max:255',
                'dns_name' => 'required|string|max:255',
                'currency' => 'required|string',
                'auto_reload' => 'required|integer',
                'idle_timeout' => 'required|integer',
                'traffic_interface' => 'required|integer',
                'live_report' => 'required|string|max:255',
            ]);

            $validated['user_id'] = $user->id;

            $router = Router::create($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Router created successfully.',
                'data' => $router,
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
                'message' => 'Failed to create router.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $router = Router::where('id', $id)->where('user_id', $user->id)->first();

            if (!$router) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Router not found or unauthorized.',
                ], 404);
            }

            $validated = $request->validate([
                'session_name' => 'sometimes|required|string|max:255',
                'ip_mikrotik' => 'sometimes|ip',
                'user_mikrotik' => 'sometimes|required|string|max:255',
                'password_' => 'sometimes|required|string|max:255',
                'hostpot_name' => 'sometimes|required|string|max:255',
                'dns_name' => 'sometimes|required|string|max:255',
                'currency' => 'sometimes|required|string',
                'auto_reload' => 'sometimes|required|integer',
                'idle_timeout' => 'sometimes|required|integer',
                'traffic_interface' => 'sometimes|required|integer',
                'live_report' => 'sometimes|required|string|max:255',
            ]);

            $router->update($validated);

            return response()->json([
                'status' => 'success',
                'message' => 'Router updated successfully.',
                'data' => $router,
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
                'message' => 'Failed to update router.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Auth::user();
            $router = Router::where('id', $id)->where('user_id', $user->id)->first();

            if (!$router) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Router not found or unauthorized.',
                ], 404);
            }

            $router->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Router deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete router.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
