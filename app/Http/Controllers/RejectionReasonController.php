<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;

class RejectionReasonController extends Controller
{
    protected $token = '98bwbb5tv6v04xazs6mkb7diu3lxc3l7';

    public function index()
    {
        $client = new Client();
        $url = 'https://10.1.94.101/rest/V1/seoudi/customer/document-status-options';

        try {
            $response = $client->request('GET', $url, [
                'headers' => ['Authorization' => 'Bearer ' . $this->token],
                'verify' => false,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            $rejectionReasons = $data[0]['rejection_reason'] ?? [];

            return view('rejection_reasons.index', compact('rejectionReasons'));
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to fetch rejection reasons.']);
        }
    }

    public function store(Request $request)
    {
        $request->validate(['optionLabel' => 'required|string']);

        $client = new Client();
        $url = 'https://10.1.94.101/rest/V1/rejectionreason/add';

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'json' => [
                    'optionLabel' => $request->input('optionLabel'),
                ],
            ]);

            // If the response status is 200, return with a success message
            if ($response->getStatusCode() === 200) {
                return redirect()->route('rejection_reasons.index')->with('success', 'Rejection reason added successfully.');
            }

            // Handle unexpected successful responses
            return redirect()->route('rejection_reasons.index')->withErrors(['error' => 'Unexpected response from the server.']);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            // Check for duplicate error (400 Bad Request)
            if ($e->getResponse()->getStatusCode() === 400) {
                $errorMessage = json_decode($e->getResponse()->getBody()->getContents(), true)['message'] ?? 'The reason already exists.';

                return back()->withErrors(['error' => $errorMessage]);
            }

            // Handle other exceptions
            return back()->withErrors(['error' => 'Failed to add rejection reason.']);
        }
    }

    public function destroy($optionId)
    {
        $client = new Client();
        $url = 'https://10.1.94.101/rest/V1/rejectionreason/remove';

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'json' => [
                    'optionId' => $optionId,
                ],
            ]);

            return redirect()->route('rejection_reasons.index')->with('success', 'Rejection reason deleted successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete rejection reason.']);
        }
    }
}
