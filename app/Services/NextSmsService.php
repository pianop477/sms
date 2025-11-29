<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NextSmsService
{
    protected $apiUsername;
    protected $apiPassword;

    public function __construct()
    {
       $this->apiUsername = 'Piano';
       $this->apiPassword = 'Veronica 24#';
    }

    public function sendSmsByNext($sender, $destination, $message, $reference)
    {
        try {
            $url = "https://messaging-service.co.tz/api/sms/v1/text/single";
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

            if($response->failed()) {
                return [
                    'success' => false,
                    'error' => $response->body()
                ];
            }

            return [
                'success' => true,
                'data' => $response->json()
            ];
        }
        catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

}
