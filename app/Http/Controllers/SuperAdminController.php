<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agent;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;


class SuperAdminController extends Controller
{
    public function usersGet()
    {
        return response()->json(User::all());
    }

    public function agentsGet()
    {
        return response()->json(Agent::all());
    }

    public function transaksisGet()
    {
        $groupedData = Transaksi::select(
            'agent_id',
            DB::raw('SUM(jumlah) as total_jumlah'),
            DB::raw('SUM(total_price) as total_harga')
        )
            ->groupBy('agent_id')
            ->get()
            ->map(function ($item) {
                // Ambil semua ID transaksi detail berdasarkan agent_id
                $detailIds = Transaksi::where('agent_id', $item->agent_id)->pluck('id')->toArray();

                return [
                    'agent_id' => $item->agent_id,
                    'total_jumlah' => $item->total_jumlah,
                    'total_harga' => $item->total_harga,
                    // 'Detail_ID_Transaksi' => $detailIds,
                ];
            });

        return response()->json([
            'data' => $groupedData
        ]);
    }

    public function detailTransaksisGet()
    {
        $data = \App\Models\DetailTransaksi::all()->map(function ($item) {
            return [
                'id' => $item->id,
                'transaksi_id' => $item->transaksi_id,
                'server' => $item->server,
                'user' => $item->user,
                'ipAddress' => $item->address, // Ubah dari 'address' ke 'ipAddress'
                'mac' => $item->mac,
                'uptime' => $item->uptime,
                'bytes_in' => $item->bytes_in,
                'bytes_out' => $item->bytes_out,
                'time_left' => $item->time_left,
                'login_by' => $item->login_by,
                'comment' => $item->comment,
                'created_at' => $item->created_at->toDateTimeString(),
                'updated_at' => $item->updated_at->toDateTimeString(),
            ];
        })->values(); // Tambahan .values() untuk reset index jika perlu

        return response()->json([
            'data' => $data,
        ]);
    }

    // Get All Router For Admin
    public function routersGet()
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden: Only admin can access all routers.',
                ], 403);
            }

            $routers = Router::all();

            return response()->json([
                'status' => 'success',
                'data' => $routers,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch routers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Get All Voucher Packet for Admin
    public function paketVouchersGet()
    {
        try {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Forbidden: Only admin can access all voucher packets.',
                ], 403);
            }

            $pakets = PaketVoucher::all();

            return response()->json([
                'status' => 'success',
                'data' => $pakets,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch vouchers.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
