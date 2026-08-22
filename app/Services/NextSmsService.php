<?php

namespace App\Services;

use App\Models\school;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NextSmsService
{
    protected $apiUsername;
    protected $apiPassword;

    public function __construct()
    {
        $this->apiUsername = config('services.next_sms.username');
        $this->apiPassword = config('services.next_sms.password');
    }

    public function sendSmsByNext($sender, $destination, $message, $reference)
    {
        try {
            $url = config('services.next_sms.base_url') . '/text/single';
            $postData = [
                'from' => $sender,
                'to' => $destination,
                'text' => $message,
                'reference' => $reference,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiUsername . ':' . $this->apiPassword),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, $postData);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }

            return [
                'success' => true,
                'data' => $response->json()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function checkBalance()
    {
        try {
            $url = config('services.next_sms.base_url') . '/balance';

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiUsername . ':' . $this->apiPassword),
                'Accept' => 'application/json',
            ])->get($url);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }

            return [
                'success' => true,
                'data' => $response->json()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function smsLogs($from, $limit, $offset)
    {
        $url = config('services.next_sms.base_url') . '/logs';

        try {

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode($this->apiUsername . ':' . $this->apiPassword),
                'Accept' => 'application/json',
            ])->get($url, [
                'from' => $from,
                'limit' => $limit,
                'offset' => $offset,
            ]);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }

            return [
                'success' => true,
                'data' => $response->json()
            ];
        } catch (Exception $e) {
            Log::error('Error fetching SMS logs: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
