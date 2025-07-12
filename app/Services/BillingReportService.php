<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserList;
use App\Models\BillingReport;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BillingReportService
{
    public function generateUserBillingReport($userId, string $date)
    {
        try {
            $user = User::findOrFail($userId);
            
            // Hitung total pendapatan dari user ini dan semua child-nya
            $total = $this->calculateTotalEarnings($user, $date);

            if ($total['total_vouchers'] == 0) {
                \Log::info("User {$user->id} tidak memiliki penjualan voucher pada {$date}.");
                return null;
            }

            // Buat atau update billing report
            $report = BillingReport::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'keterangan' => 'Billing voucher harian for reseller ' . $user->user . ' and agents ' . $date,
                ],
                [
                    'qty' => $total['total_vouchers'],
                    'bill_amount' => $total['total_amount'],
                    'is_sent' => false
                ]
            );

            return $report;

        } catch (\Exception $e) {
            \Log::error('Error generating billing report for user ' . $userId . ': ' . $e->getMessage());
            return null;
            // return response()->json([
            //     'message' => 'Terjadi kesalahan saat menghasilkan laporan billing.',
            //     'error' => $e->getMessage(),
            //     'user_id' => $userId,
            //     'date' => $date
            // ], 500);
        }
    }


    public function generateAllUserBillingReport(string $date)
    {
        $result = [];
        // Ambil semua parent users (yang id_parent null)
        $parentUsers = User::whereNull('id_parent')->get();

        foreach ($parentUsers as $user) {
            try{
                $report = $this->generateUserBillingReport($user->id, $date);
                if($report){
                    $apiResponse = $this->sendBillingToExternalApi($report);
                    
                    $results[] = [
                        'user_id' => $user->id,
                        'report' => $report,
                        'api_response' => $apiResponse
                    ];
                } else {
                    $results[] = [
                        'user_id' => $user->id,
                        'user_name' => $user->user,
                        'message' => 'Tidak ada penjualan voucher untuk user ini per hari ini',
                        'date' => $date
                    ];
                }
            }catch (\Exception $e) {
                // Log error jika ada masalah dengan user ini
                \Log::error('Error generating billing report for user ' . $user->id . ': ' . $e->getMessage());
                $results[] = [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'error' => $e->getMessage(),
                    'date' => $date
                ];
                continue;
            }
        }

        return $results;
    }

    private function calculateTotalEarnings(User $user, string $date)
    {
        // Dapatkan semua child users (termasuk nested children jika ada)
        $userIds = $this->getAllChildUserIds($user);
        $userIds[] = $user->id; // Tambahkan user parent

        $startDate = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
        $endDate = Carbon::createFromFormat('Y-m-d', $date)->endOfDay();

        // Hitung total voucher yang digunakan
        $vouchers = UserList::whereIn('user_id', $userIds)
            ->where('is_used', true)
            ->whereBetween('used_at', [$startDate, $endDate])
            ->with('paketVouchers')
            ->get();

        $totalAmount = 0;
        $totalVouchers = $vouchers->count();

        foreach ($vouchers as $voucher) {
            $totalAmount += $voucher->paketVouchers->price;
        }

        return [
            'total_vouchers' => $totalVouchers,
            'total_amount' => $totalAmount
        ];
    }

    private function getAllChildUserIds(User $user)
    {
        $userIds = [];
        
        // Fungsi rekursif untuk mendapatkan semua child
        $this->getChildrenIds($user, $userIds);
        
        return $userIds;
    }

    private function getChildrenIds(User $user, array &$userIds)
    {
        $children = User::where('id_parent', $user->id)->get();
        
        foreach ($children as $child) {
            $userIds[] = $child->id;
            $this->getChildrenIds($child, $userIds);
        }
    }

        /**
     * Send single billing report to external API
     */
    public function sendBillingToExternalApi(BillingReport $report)
    {
        $data = [
            'user_id' => $report->user_id,
            'keterangan' => $report->keterangan,
            'qty' => $report->qty,
            'bill_amount' => $report->bill_amount,
            'timestamp' => $report->created_at->toDateTimeString()
        ];

        try {
            $response = Http::withToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImQ4NDcwZDU1Yzc1NDljM2U5NGFkNzI5OWRiMjFlM2I0YzFjOTNlMWYwYWE1YzY4MzhjNGQ2MjU3NDE3YWExYzc5OGQ4YjM3OTJhNTAzNDAwIn0.eyJhdWQiOiIxIiwianRpIjoiZDg0NzBkNTVjNzU0OWMzZTk0YWQ3Mjk5ZGIyMWUzYjRjMWM5M2UxZjBhYTVjNjgzOGM0ZDYyNTc0MTdhYTFjNzk4ZDhiMzc5MmE1MDM0MDAiLCJpYXQiOjE3NDk1NDM2NjksIm5iZiI6MTc0OTU0MzY2OSwiZXhwIjoxNzQ5NjMwMDY5LCJzdWIiOiIxMiIsInNjb3BlcyI6W119.Nr_oAYqDtxBHQO3gxrob20s9ymrCq_lctsxz0777pbluDr1BncsXBRewsNyKzRuHneVADrclfs5f9JP0pGpSCvlbtxLtBi0O_AgyouUKjMp-VQW-IGBUTarO5t56ifrq3a0Lz_BiTfFglFleJEQxqQzoW2XHuIa-4ikAkfHT2WHd5S80e3cohVrDIt-lBeMcpIhDu4jzyTrlBBOSV4nUcqp0HDnWtDQheYF4Zeb02urUzKgDAL03h6o7OGSNL29BD-_iNYG31hEbC-ccllfpAFBpiVv5kTewJEUxaK0xYW_DybWTSP_nRDL-TDuoWsiXOM9YnyxtSSVzkqHPTp-luvzGOW5g9q6kxhOAtqulHugNa5Xb29cRwlyQSyXJmaU6Rua0D0zwX7-HihKdfkyHs0OqBpSXkDLnsvC0eLlboxJefxLzESjii_I1SoWu-g0EekhxrikMaalQZxbmhVq6O7XttKXXGK2W8VjgBB8Ky8tK-lTVujGUd8Yza3LtZMZTRrkVU29SEtTl5cZf0SwKA0EDgVLic4Xrvh_YpSpEjz4ye7r_vS6KJXJvGEfJBh1HWCEFqccvVc4gVFqi4d-bFYUqkvbx7NeruMr11dMDaUWnuD43ikBTktoehapd6HrunNxRlMgQvNc4_XXfw61_sPEYv-OV9cFUH4pSELV7o44')
            ->post('http://devpanel.jinom.net/api/billing/create', [
                'json' => $data,
                // 'headers' => [
                //     'Authorization' => 'Bearer ' . config('services.external_api.token'),
                //     'Accept' => 'application/json'
                // ]
            ]);

            $responseData = json_decode($response->getBody(), true);
            
            if ($response->getStatusCode() == 200) {
                $report->update(['is_sent' => true]);
                return [
                    'success' => true,
                    'response' => $responseData
                ];
            }

            return [
                'success' => false,
                'error' => 'API returned status: ' . $response->getStatusCode(),
                'response' => $responseData
            ];

        } catch (\Exception $e) {
            Log::error('Failed to send billing report: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'response' => null
            ];
        }
    }

    public function resendFailedBillingReports(): array
    {
        $failedReports = $this->filterBillingReportsByIsSent(false);
        $results = [];

        foreach ($failedReports as $report) {
            $result = $this->sendBillingToExternalApi($report);

            $results[] = [
                'report_id' => $report->id,
                'user_id' => $report->user_id,
                'success' => $result['success'] ?? false,
                'message' => $result['success'] ? 'Resent successfully' : ($result['error'] ?? 'Unknown error')
            ];
        }

        return $results;
    }


    private function getAllBillingReports()
    {
        try{
            $reports = BillingReport::all();
            return $reports;
        } catch (\Exception $e) {
            \Log::error('Error fetching all billing reports: ' . $e->getMessage());
            return [];
        }
    }

    private function filterBillingReportsByIsSent(bool $isSent)
    {
        try {
            $reports = BillingReport::where('is_sent', $isSent)->get();
            return $reports;
        } catch (\Exception $e) {
            \Log::error('Error filtering billing reports: ' . $e->getMessage());
            return [];
        }
    }
}