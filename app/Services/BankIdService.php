<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class BankIdService
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.bankid.url');
    }

    public function initiateAuthentication($personalNumber)
    {
        try {
            $response = Http::withOptions([
                'verify' => false,
                'cert' => env('BANKID_CLIENT_CERT'),
                'ssl_key' => env('BANKID_CLIENT_KEY'),
            ])->post(env('BANKID_API_URL') . '/auth', [
                'personalNumber' => $personalNumber,
                'endUserIp' => request()->ip(),
            ]);
            
            if ($response->status() == 400 && $response->json()['errorCode'] == 'alreadyInProgress') {
                return response()->json(['message' => 'An authentication is already in progress. Please wait for it to complete or try again later.'], 400);
            }
            
           

            return $response->json();
        } catch (\Exception $e) {
            \Log::error('BankID API Error', ['message' => $e->getMessage()]);
            return response()->json(['error' => 'An error occurred'], 500);
        }
    }


    public function checkAuthenticationStatus($orderRef)
    {
        $response = Http::withOptions([
            'verify' => false,
            'cert' => env('BANKID_CLIENT_CERT'),
            'ssl_key' => env('BANKID_CLIENT_KEY'),
        ])->post(env('BANKID_API_URL')."/collect", [
            'orderRef' => $orderRef,
        ]);

        return $response->json();
    }
}
