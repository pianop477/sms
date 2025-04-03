<?php

namespace App\Services;

class VirusTotal
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = "7827171040f531d3cfe1ec05b05698819c3c1dcb7e476e1a26622a9fb646bbb1";
        $this->baseUrl = 'https://www.virustotal.com/vtapi/v2/';
    }

    public function scanFile($filePath)
    {
        $url = $this->baseUrl . 'file/scan';
        $params = [
            'apikey' => $this->apiKey,
            'file' => new \CURLFile($filePath),
        ];

        // return $this->makeRequest($url, $params);
    }
}
