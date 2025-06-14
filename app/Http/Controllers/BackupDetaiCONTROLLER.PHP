<?php

namespace App\Http\Controllers;
use Illuminate\Http\Response;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\RouterosAPI;
class DetailTransaksiController extends Controller
{
    public function index()
    {
        $details = DetailTransaksi::with('transaksi.agent')->get();
        return response()->json($details);
    }

    public function store(Request $request)
{
    $request->validate([
        'transaksi_id' => 'nullable|exists:transaksi,id',
        'agent_id' => 'nullable|exists:agents,id',
        'details.*.user' => 'nullable|string', // name → user
        'details.*.password' => 'nullable|string',
        'details.*.remark' => 'nullable|string', // comment → remark
        'details.*.profile' => 'nullable|string',
        'details.*.time_limit' => 'nullable|string', // uptime → time_limit
        'details.*.price' => 'nullable|numeric',
    ]);

    if (!$request->transaksi_id) {
        $transaksi = Transaksi::create([
            'agent_id' => $request->agent_id,
            'jumlah' => 0, // Akan dihitung ulang nanti
            'total_price' => 0
        ]);
        $transaksi_id = $transaksi->id;
    } else {
        $transaksi_id = $request->transaksi_id;
    }

    $details = [];

    foreach ($request->details as $detail) {
        $details[] = DetailTransaksi::create([
            'transaksi_id' => $transaksi_id,
            'user' => $detail['user'],
            'password' => $detail['password'],
            'remark' => $detail['remark'] ?? null,
            'profile' => $detail['profile'] ?? null,
            'time_limit' => $detail['time_limit'] ?? null,
            'price' => $detail['price'],
        ]);
    }

    $this->recalculateTransaksi($transaksi_id);
    
    return response()->json(['message' => 'Detail transaksi berhasil dibuat', 'data' => $details], 201);
}

    /**
     * 🔥 Fungsi untuk menghitung ulang jumlah dan total_price transaksi
     */
    private function recalculateTransaksi($transaksi_id)
    {
        $transaksi = Transaksi::find($transaksi_id);
        if ($transaksi) {
            // Hitung jumlah detail transaksi
            $jumlah = DetailTransaksi::where('transaksi_id', $transaksi_id)->count();
            // Hitung total harga dari semua detail transaksi
            $totalPrice = DetailTransaksi::where('transaksi_id', $transaksi_id)->sum('price');

            // Update transaksi
            $transaksi->update([
                'jumlah' => $jumlah,
                'total_price' => $totalPrice
            ]);
        }
    }


    public function show($id)
    {
        $detail = DetailTransaksi::with('transaksi.agent')->find($id);
        if (!$detail) {
            return response()->json(['message' => 'Detail transaksi tidak ditemukan'], 404);
        }
        return response()->json($detail);
    }

    public function update(Request $request, $id)
    {
        $detail = DetailTransaksi::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Detail transaksi tidak ditemukan'], 404);
        }

        $request->validate([
            'name' => 'sometimes|string',
            'password' => 'sometimes|string',
            'comment' => 'sometimes|string',
            'profile' => 'sometimes|string',
            'uptime' => 'sometimes|string',
        ]);

        $detail->update($request->all());
        return response()->json($detail);
    }

    public function destroy($id)
    {
        $detail = DetailTransaksi::find($id);
        if (!$detail) {
            return response()->json(['message' => 'Detail transaksi tidak ditemukan'], 404);
        }
        $detail->delete();
        return response()->json(['message' => 'Detail transaksi berhasil dihapus']);
    }
}
