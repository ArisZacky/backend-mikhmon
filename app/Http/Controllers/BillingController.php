<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Transaksi;

class BillingController extends Controller
{
    public function getBillingInfo(): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $transaksi = Transaksi::with('agent')
            ->whereHas('agent', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->first();

        if (!$transaksi) {
            return response()->json(['error' => 'Transaksi tidak ditemukan untuk user ini'], 404);
        }


        $externalApiResponse = $this->postDataToExternalApi($transaksi, $user);

        return response()->json([
            'keterangan' => 'Pembayaran Voucher Agents ' . ($transaksi->agent->nama ?? 'Agent Tidak Diketahui'),
            'bill_amount' => (string) ($transaksi->total_price ?? 0),
            'user_id' => $user->id,
            'agent' => $transaksi->agent,
            'external_api_response' => $externalApiResponse,
        ]);
    }

    private function postDataToExternalApi($transaksi, $user)
    {
        $response = Http::post('http://devpanel.jinom.net/api/billing/create', [
            'keterangan' => 'Pembayaran Voucher Agents ' . ($transaksi->agent->nama ?? 'Agent Tidak Diketahui'),
            'bill_amount' => (string) ($transaksi->total_price ?? 0),
            'user_id' => $user->id,            
        ]);

        
        return $response->json();
    }
}
