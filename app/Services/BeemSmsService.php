<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BeemSmsService
{
    protected $apiKey;
    protected $secretKey;
    protected $baseUrl;

    public function __construct()
    {
       $this->apiKey = '947863ca54be8767';
       $this->secretKey = 'YjJhZjcyYmIxNzllNDlmMDhiOTFkNzRlMTUyN2IwNDdiN2NhYWEyNGNiYWE5MjhhYmViMjRhZGQwMzc4MjRjOA==';
       $this->baseUrl = "https://apisms.beem.africa/v1/send";
    }

    public function sendSms($sourceAddr, $message, $recipients)
    {
        try {
            // Prepare the request payload (as per API documentation)
            $postData = [
                'source_addr' => $sourceAddr, // Correctly set the source address
                'schedule_time' => '',
                'encoding' => 0, // Ensure this is an integer, not a string
                'message' => $message,
                'recipients' => $recipients, // Correctly set the recipients array
            ];

            // Log::info($postData);

            // Make the API request
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode("{$this->apiKey}:{$this->secretKey}"),
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, $postData);

            // Check for errors in the response
            if ($response->failed()) {
                throw new Exception($response->body());
            }

            // Return the API response
            return $response->json();
        } catch (Exception $e) {
            // Handle exceptions
            throw new Exception($e->getMessage());
        }
    }

}
