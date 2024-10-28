<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use GuzzleHttp\Client;

class CustomerController extends Controller
{
    public function index()
    {
        // Initialize Guzzle Client
        $client = new Client();

        // Define API endpoint and token
        $url = 'https://10.1.94.101/rest/V1/customers/search?searchCriteria=[]';
        $token = '5ffs6yf7snkspg1z99o7zhriubn92at8';

        // Make API request
        $response = $client->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
            ],
            'verify' => false, // Skip SSL certificate verification if needed
        ]);

        // Decode JSON response into an array
        $data = json_decode($response->getBody()->getContents(), true);

        // Extract the "items" array
        $customers = $data['items'];
        //dd($customers);
        foreach ($customers as &$customer) {
            $groupId = $customer['group_id'];

            // Fetch the group code from the database using the group_id
            $customerGroup = CustomerGroup::where('group_id', $groupId)->first();

            // Add the group code to the customer array
            $customer['group_code'] = $customerGroup ? $customerGroup->code : 'Unknown';
        }

        // Pass data to the view
        return view('customers.index', compact('customers'));
    }
}
