<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class RejectionReasonService
{
    protected $client;
    protected $apiBaseUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiBaseUrl = config('services.customer.api_base_url'); // Configure in `services.php`
        $this->apiToken = config('services.customer.api_token');
    }

    public function getRejectionReasons()
    {
        $url = "{$this->apiBaseUrl}/seoudi/customer/document-status-options";

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => ['Authorization' => 'Bearer ' . $this->apiToken],
                'verify' => false,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data[0]['rejection_reason'] ?? [];
        } catch (\Exception $e) {
            Log::error("Failed to fetch rejection reasons: " . $e->getMessage());
            return [];
        }
    }

    public function addRejectionReason($optionLabel)
    {
        $url = "{$this->apiBaseUrl}/rejectionreason/add";

        try {
            $response = $this->client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'json' => ['optionLabel' => $optionLabel],
            ]);

            return $response->getStatusCode() === 200;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() === 400) {
                $errorMessage = json_decode($e->getResponse()->getBody()->getContents(), true)['message'] ?? 'The reason already exists.';
                throw new \Exception($errorMessage);
            }

            throw $e;
        }
    }

    public function removeRejectionReason($optionId)
    {
        $url = "{$this->apiBaseUrl}/rejectionreason/remove";

        try {
            $this->client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'json' => ['optionId' => $optionId],
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to delete rejection reason: " . $e->getMessage());
            return false;
        }
    }
}
