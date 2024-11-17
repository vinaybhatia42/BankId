<?php

namespace App\Http\Controllers;

use App\Services\BankIdService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCode;


class BankIdController extends Controller
{
    protected $bankIdService;

    public function __construct(BankIdService $bankIdService)
    {

        $this->bankIdService = $bankIdService;
    }

    public function checkBank(Request $request)
    {

        $request->validate([
            'personalNumber' => 'required|digits:12',
        ]);

        $response = $this->bankIdService->initiateAuthentication($request->personalNumber);
        dd($response);
        if (isset($response['orderRef'])) {
            return response()->json([
                'orderRef' => $response['orderRef'],
                'qrCode' => $this->generateQrCode($response['qrStartSecret'], $response['qrStartToken']),
                'signature' => $response['signature'] ?? 'No signature found',

                'message' => 'Authentication initiated successfully.',
            ]);
        }

        return response()->json([
            'error' => $response['errorMessage'] ?? 'Failed to initiate authentication.',
        ], 400);
    }
    private function generateQrCode($qrStartSecret, $qrStartToken)
    {
        // BankID-specific QR code format
        $qrCodeData = "bankid:///?token={$qrStartToken}&secret={$qrStartSecret}";

        // Generate the QR code URL using any QR code library (e.g., Simple QrCode in Laravel)
        return \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrCodeData);

    }

    public function checkStatus(Request $request)
    {
        $request->validate([
            'orderRef' => 'required',
        ]);

        $response = $this->bankIdService->checkAuthenticationStatus($request->orderRef);
        // dd($response);
        if (isset($response['status']) && $response['status'] === 'complete') {
            return response()->json([
                'message' => 'Authentication successful.',
                'user' => $response['user'] ?? null,
            ]);
        }

        return response()->json([
            'status' => $response['status'] ?? 'pending',
            'hint' => $response['hintCode'] ?? 'Waiting for user action.',
        ]);
    }
}
