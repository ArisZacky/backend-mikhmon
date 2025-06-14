<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Transaksi;
use App\Models\User;

class SendBillingToExternalApi extends Command
{
    protected $signature = 'billing:post-to-api';
    protected $description = 'Kirim data billing ke API eksternal';

    public function handle()
    {
        $user = User::first(); // Gunakan user asli untuk ambil data transaksi

        if (!$user) {
            $this->error('User tidak ditemukan.');
            return 1;
        }

        $transaksi = Transaksi::with('agent')
            ->whereHas('agent', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->first();

        if (!$transaksi) {
            $this->error('Transaksi tidak ditemukan untuk user ini.');
            return 1;
        }

        // Kirim ke API dengan user_id = 156 (bukan user asli)
        $response = Http::withToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImQ4NDcwZDU1Yzc1NDljM2U5NGFkNzI5OWRiMjFlM2I0YzFjOTNlMWYwYWE1YzY4MzhjNGQ2MjU3NDE3YWExYzc5OGQ4YjM3OTJhNTAzNDAwIn0.eyJhdWQiOiIxIiwianRpIjoiZDg0NzBkNTVjNzU0OWMzZTk0YWQ3Mjk5ZGIyMWUzYjRjMWM5M2UxZjBhYTVjNjgzOGM0ZDYyNTc0MTdhYTFjNzk4ZDhiMzc5MmE1MDM0MDAiLCJpYXQiOjE3NDk1NDM2NjksIm5iZiI6MTc0OTU0MzY2OSwiZXhwIjoxNzQ5NjMwMDY5LCJzdWIiOiIxMiIsInNjb3BlcyI6W119.Nr_oAYqDtxBHQO3gxrob20s9ymrCq_lctsxz0777pbluDr1BncsXBRewsNyKzRuHneVADrclfs5f9JP0pGpSCvlbtxLtBi0O_AgyouUKjMp-VQW-IGBUTarO5t56ifrq3a0Lz_BiTfFglFleJEQxqQzoW2XHuIa-4ikAkfHT2WHd5S80e3cohVrDIt-lBeMcpIhDu4jzyTrlBBOSV4nUcqp0HDnWtDQheYF4Zeb02urUzKgDAL03h6o7OGSNL29BD-_iNYG31hEbC-ccllfpAFBpiVv5kTewJEUxaK0xYW_DybWTSP_nRDL-TDuoWsiXOM9YnyxtSSVzkqHPTp-luvzGOW5g9q6kxhOAtqulHugNa5Xb29cRwlyQSyXJmaU6Rua0D0zwX7-HihKdfkyHs0OqBpSXkDLnsvC0eLlboxJefxLzESjii_I1SoWu-g0EekhxrikMaalQZxbmhVq6O7XttKXXGK2W8VjgBB8Ky8tK-lTVujGUd8Yza3LtZMZTRrkVU29SEtTl5cZf0SwKA0EDgVLic4Xrvh_YpSpEjz4ye7r_vS6KJXJvGEfJBh1HWCEFqccvVc4gVFqi4d-bFYUqkvbx7NeruMr11dMDaUWnuD43ikBTktoehapd6HrunNxRlMgQvNc4_XXfw61_sPEYv-OV9cFUH4pSELV7o44')
            ->post('http://devpanel.jinom.net/api/billing/create', [
                'keterangan' => 'Pembayaran Voucher Agents ' . ($transaksi->agent->nama ?? 'Agent Tidak Diketahui'),
                'bill_amount' => (string) ($transaksi->total_price ?? 0),
                'user_id' => 86, // Ganti user_id untuk keperluan API
            ]);

        if ($response->successful()) {
            $this->info('Data berhasil dikirim ke API eksternal. Response: ' . json_encode($response->json()));
        } else {
            $this->error('Gagal mengirim data. Status: ' . $response->status());
            $this->error('Response body: ' . $response->body());
        }

        return 0;
    }
}
