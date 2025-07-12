<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BillingReport;
use App\Services\BillingReportService;

class BillingReportController extends Controller
{
    public function __construct(protected BillingReportService $service)
    {
        $this->service = $service;
    }

    /**
     * Generate and send billing report for a specific user
     */
    public function generateAndSendReport(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'uset_at' => 'sometimes|date_format:Y-m-d' // Default hari ini
        ]);

        $date = $request->input('used_at', now()->format('Y-m-d'));

        $userId = $request->input('user_id');
        
        try {
            // Generate report
            $report = $this->service->generateUserBillingReport($userId, $date);
            
            // Send to external API
            $apiResponse = $this->service->sendBillingToExternalApi($report);
            
            return response()->json([
                'success' => true,
                'message' => 'Billing report generated and sent successfully',
                'report' => $report,
                'api_response' => $apiResponse
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate or send billing report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate and send billing reports for all parent users
     */
    public function generateAndSendAllReports(Request $request)
    {
        $request->validate([
            'used_at' => 'sometimes|date_format:Y-m' // Optional used_at parameter
        ]);

        $date = $request->input('used_at', now()->format('Y-m-d'));
        try {
            $results = $this->service->generateAllUserBillingReport($date);
            
            return response()->json([
                'success' => true,
                'message' => 'All billing reports generated and sent successfully',
                'reports' => $results
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate or send billing reports',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function resendFailedReports()
    {
        try{
            $results = $this->service->resendFailedBillingReports();

            return response()->json([
                'success' => true,
                'message' => 'All failed billing reports resent successfully',
                'reports' => $results
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to resend billing reports',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
