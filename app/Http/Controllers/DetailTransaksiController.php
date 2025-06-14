<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use App\Models\DetailTransaksi;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\RouterosAPI;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;



class DetailTransaksiController extends Controller
{
    public function index()
    {
        $users = DetailTransaksi::all();
        return response()->json($users);
    }
    public function getDetail()
    {
        $users = DetailTransaksi::all();
        return response()->json($users);
    }
    public function postData(Request $request)
    {
        Log::info("masuk cuk");
        $request->validate([
            'transaksi_id' => 'nullable|exists:transaksi,id',
            'agent_id' => 'nullable|exists:agents,id',
            'details.*.server' => 'nullable|string',
            'details.*.user' => 'nullable|string',
            'details.*.address' => 'nullable|string',
            'details.*.mac' => 'nullable|string',
            'details.*.uptime' => 'nullable|string',
            'details.*.bytes_in' => 'nullable|string',
            'details.*.bytes_out' => 'nullable|string',
            'details.*.time_left' => 'nullable|string',
            'details.*.login_by' => 'nullable|string',
            'details.*.comment' => 'nullable|string',
        ]);

        if (!$request->transaksi_id) {
            $transaksi = Transaksi::create([
                'agent_id' => $request->agent_id,
                'jumlah' => 0,
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
                'server' => $detail['server'] ?? null,
                'user' => $detail['user'] ?? null,
                'address' => $detail['address'] ?? null,
                'mac' => $detail['mac'] ?? null,
                'uptime' => $detail['uptime'] ?? null,
                'bytes_in' => $detail['bytes_in'] ?? null,
                'bytes_out' => $detail['bytes_out'] ?? null,
                'time_left' => $detail['time_left'] ?? null,
                'login_by' => $detail['login_by'] ?? null,
                'comment' => $detail['comment'] ?? null,
            ]);
        }

        $this->recalculateTransaksi($transaksi_id);

        return response()->json(['message' => 'Detail transaksi berhasil dibuat', 'data' => $details], 201);
    }
    public function store(Request $request)
    {
        $request->validate([
            'transaksi_id' => 'nullable|exists:transaksi,id',
            'agent_id' => 'nullable|exists:agents,id',
            'details.*.server' => 'nullable|string',
            'details.*.user' => 'nullable|string',
            'details.*.address' => 'nullable|string',
            'details.*.mac' => 'nullable|string',
            'details.*.uptime' => 'nullable|string',
            'details.*.bytes_in' => 'nullable|string',
            'details.*.bytes_out' => 'nullable|string',
            'details.*.time_left' => 'nullable|string',
            'details.*.login_by' => 'nullable|string',
            'details.*.comment' => 'nullable|string',
        ]);

        if (!$request->transaksi_id) {
            $transaksi = Transaksi::create([
                'agent_id' => $request->agent_id,
                'jumlah' => 0,
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
                'server' => $detail['server'] ?? null,
                'user' => $detail['user'] ?? null,
                'address' => $detail['address'] ?? null,
                'mac' => $detail['mac'] ?? null,
                'uptime' => $detail['uptime'] ?? null,
                'bytes_in' => $detail['bytes_in'] ?? null,
                'bytes_out' => $detail['bytes_out'] ?? null,
                'time_left' => $detail['time_left'] ?? null,
                'login_by' => $detail['login_by'] ?? null,
                'comment' => $detail['comment'] ?? null,
            ]);
        }
        foreach ($details as $detail) {
            Log::info("Transaction Detail Comment: " . ($detail->comment ?? 'No Comment'));
        }
        
        LOG::info($details['comment']);
        $this->recalculateTransaksi($transaksi_id);

        return response()->json(['message' => 'Detail transaksi berhasil dibuat', 'data' => $details], 201);
    }

    private function recalculateTransaksi($transaksi_id)
{
    $transaksi = Transaksi::find($transaksi_id);

    if ($transaksi) {
        
        $jumlah = DetailTransaksi::where('transaksi_id', $transaksi_id)->count();

        
        $details = DetailTransaksi::where('transaksi_id', $transaksi_id)->get();

        
        foreach ($details as $detail) {
            Log::info("DetailTransaksi ID: {$detail->id}, Comment: {$detail->comment}");
        }

        
        $total_price = $details->sum(function ($detail) {
            $comment = $detail->comment;

            
            preg_match('/(\d+)$/', $comment, $matches);
            $price = isset($matches[1]) ? (float) $matches[1] : 0;

            Log::info("Extracted Price from Comment '{$comment}': {$price}");

            return $price;
        });

        // Logging hasil perhitungan
        Log::info("Transaksi ID: {$transaksi_id}, Jumlah: {$jumlah}, Total Price: {$total_price}");

        // Update transaksi dengan jumlah dan total price
        $transaksi->update([
            'jumlah' => $jumlah,
            'total_price' => $total_price
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
            'server' => 'sometimes|string',
            'user' => 'sometimes|string',
            'address' => 'sometimes|string',
            'mac' => 'sometimes|string',
            'uptime' => 'sometimes|string',
            'bytes_in' => 'sometimes|string',
            'bytes_out' => 'sometimes|string',
            'time_left' => 'sometimes|string',
            'login_by' => 'sometimes|string',
            'comment' => 'sometimes|string',
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
