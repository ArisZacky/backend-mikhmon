<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agent;
use App\Models\Transaksi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'user' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'user' => $request->user,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'Reseller registered successfully!'], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'user' => 'required',
            'password' => 'required'
        ]);

        if (!$token = Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = Auth::user();

        session([
            'user_id' => $user->id,
            'user' => $user->user,
            'email' => $user->email,
            'password' => $user->password,
            'token' => $token
        ]);

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user,
            'session_data' => [
                'user_id' => $user->id,
                'user' => $user->user,
                'email' => $user->email,
                'pass' => request('password'),
                'token' => $token,
            ]
        ]);
    }

    public function getUserData(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $agents = Agent::where('user_id', $user->id)->get();
        $transaksi = Transaksi::where('user_id', $user->id)->with('agent')->get();

        return response()->json([
            'user' => $user,
            'agents' => $agents,
            'transaksi' => $transaksi
        ]);
    }
}
