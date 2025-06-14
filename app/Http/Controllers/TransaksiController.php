<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\DetailTransaksi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransaksiController extends Controller
{
    // Menampilkan semua transaksi
    public function index(): JsonResponse
    {
        $transaksi = Transaksi::with(['agent', 'detailTransaksi'])->get();
        return response()->json($transaksi);
    }

    // Menyimpan transaksi baru
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        // Buat transaksi baru dengan total_price = 0 dulu
        $transaksi = Transaksi::create([
            'agent_id' => $request->agent_id,
            'jumlah' => $request->jumlah,
            'total_price' => 0
        ]);

        // Hitung ulang total_price setelah transaksi dibuat
        $this->recalculateTotalPrice($transaksi->id);

        return response()->json(['message' => 'Transaksi berhasil dibuat', 'data' => $transaksi], 201);
    }

    public function createTX(Request $request): JsonResponse
    {
        $request->validate([
            'agent_id' => 'required|exists:agents,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        // Buat transaksi baru dengan total_price = 0 dulu
        $transaksi = Transaksi::create([
            'agent_id' => $request->agent_id,
            'jumlah' => $request->jumlah,
            'total_price' => 0
        ]);

        // Hitung ulang total_price setelah transaksi dibuat
        $this->recalculateTotalPrice($transaksi->id);

        return response()->json(['message' => 'Transaksi berhasil dibuat', 'data' => $transaksi], 201);
    }

    public function getData(): JsonResponse
    {
        $groupedData = Transaksi::with('agent')
            ->where('is_sent_to_contract', false) // ⬅️ hanya data yang belum dikirim
            ->get()
            ->groupBy('agent_id')
            ->map(function ($items) {
                return [
                    'agent_id' => $items->first()->agent_id,
                    'total_jumlah' => $items->sum('jumlah'),
                    'total_harga' => $items->sum('total_price'),
                    'Detail_ID_Transaksi' => $items->pluck('id'), // untuk update nanti
                ];
            })
            ->values(); // reset index jadi array biasa

        return response()->json($groupedData);
    }



    // Menampilkan detail transaksi berdasarkan ID
    public function show($id): JsonResponse
    {
        $transaksi = Transaksi::with(['agent', 'detailTransaksi'])->find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json($transaksi);
    }

    // Mengupdate transaksi berdasarkan ID
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'agent_id' => 'exists:agents,id',
            'jumlah' => 'integer|min:1',
        ]);

        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Update data transaksi
        $transaksi->update($request->only(['agent_id', 'jumlah']));

        // Hitung ulang total_price dari detail transaksi
        $this->recalculateTotalPrice($id);

        return response()->json(['message' => 'Transaksi berhasil diperbarui', 'data' => $transaksi]);
    }

    // Menghapus transaksi berdasarkan ID
    public function destroy($id): JsonResponse
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Hapus detail transaksi terlebih dahulu untuk menjaga referensial integrity
        $transaksi->detailTransaksi()->delete();

        // Hapus transaksi utama
        $transaksi->delete();

        return response()->json(['message' => 'Transaksi berhasil dihapus']);
    }

    // 🔥 Method untuk Menghitung Ulang total_price
    private function recalculateTotalPrice($transaksi_id)
    {
        $transaksi = Transaksi::find($transaksi_id);
        if ($transaksi) {
            $totalPrice = $transaksi->detailTransaksi()->sum('price');
            $transaksi->update(['total_price' => $totalPrice]);
        }
    }
    public function markAsSent(Request $request): JsonResponse
    {
        $ids = $request->input('ids');

        Transaksi::whereIn('id', $ids)->update(['is_sent_to_contract' => true]);

        return response()->json(['message' => 'Transaksi ditandai sudah dikirim.']);
    }
}
