<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerCreditService
{
    protected $client;
    protected $apiBaseUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiBaseUrl = config('services.customer.api_base_url');
        $this->apiToken = config('services.customer.api_token');
    }

    public function getCustomerCredit($customerId)
    {
        try {
            $response = $this->client->request('GET', "{$this->apiBaseUrl}/customerStoreCredit/getByCustomerId", [
                'query' => ['customer_id' => $customerId],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                ],
                'verify' => false,
            ]);

            $data = json_decode($response->getBody(), true);

            // Check if 'customer_store_credit' exists and has a valid customer_id
            if (!isset($data[0]['customer_store_credit']['customer_id']) || is_null($data[0]['customer_store_credit']['customer_id'])) {
                return null;
            }

            return $data[0]['customer_store_credit'];

        } catch (RequestException $e) {
            Log::error('API request failed: ' . $e->getMessage());
            return null;
        }
    }

    public function createCustomerCredit($data)
    {
        try {
            $response = $this->client->request('POST', "{$this->apiBaseUrl}/customerStoreCredit", [
                'json' => ['input' => $data],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);

            return true;
        } catch (RequestException $e) {
            Log::error('Error creating customer credit: ' . $e->getMessage());
            return false;
        }
    }

    public function updateCustomerCredit($data)
    {
        try {
            $response = $this->client->request('PUT', "{$this->apiBaseUrl}/customerStoreCredit", [
                'json' => ['input' => $data],
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
            ]);

            return true;
        } catch (RequestException $e) {
            Log::error('Error updating customer credit: ' . $e->getMessage());
            return false;
        }
    }
    public function getCreditCategories()
    {
        return DB::table('credit_categories')->select('id', 'category_name', 'value')->get();
    }

    public function mapCreditValueToCategory($value, $categories)
    {
        foreach ($categories as $category) {
            if ($category->value == $value) {
                return $category->category_name;
            }
        }
        return 'Unknown';
    }
}
