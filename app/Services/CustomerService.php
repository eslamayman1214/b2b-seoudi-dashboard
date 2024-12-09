<?php

namespace App\Services;

use App\Mail\RejectionReasonMail;
use App\Models\CustomerGroup;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerService
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

    public function getCustomers(Request $request)
    {
        $client = new Client();
        $url = "{$this->apiBaseUrl}/customers/search?searchCriteria=[]";
        $perPage = $request->input('perPage', 25);
        $statusFilter = $request->input('document_status', null);

        $response = $client->request('GET', $url, [
            'headers' => ['Authorization' => 'Bearer ' . $this->apiToken],
            'verify' => false,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $customers = collect($data['items']);

        if ($statusFilter) {
            $customers = $customers->filter(function ($customer) use ($statusFilter) {
                $documentStatusCode = collect($customer['custom_attributes'])->firstWhere('attribute_code', 'document_status')['value'] ?? '';
                return $documentStatusCode == $statusFilter;
            });
        }

        $customers = $customers->map(function ($customer) {
            $groupId = $customer['group_id'];
            $customerGroup = CustomerGroup::where('group_id', $groupId)->first();
            $customer['group_code'] = $customerGroup ? $customerGroup->code : 'Unknown';
            return $customer;
        });

        $customersPaginated = new LengthAwarePaginator(
            $customers->forPage($request->page ?? 1, $perPage),
            $customers->count(),
            $perPage,
            $request->page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Fetch document status options
        $statusOptions = $this->getDocumentStatusOptions();

        return view('customers.index', compact('customersPaginated', 'perPage', 'statusFilter', 'statusOptions'));
    }

    public function downloadDocument($customerId)
    {
        $url = "{$this->apiBaseUrl}/admin/customers/document/download?customerId=$customerId";

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => ['Authorization' => 'Bearer ' . $this->apiToken],
                'verify' => false,
                'stream' => true,
            ]);

            return response($response->getBody(), 200)
                ->header('Content-Type', 'application/zip')
                ->header('Content-Disposition', "attachment; filename=document_CustomerID_{$customerId}.zip");
        } catch (\Exception $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }

    public function getDocumentStatusOptions()
    {
        $url = "{$this->apiBaseUrl}/seoudi/customer/document-status-options";

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => ['Authorization' => 'Bearer ' . $this->apiToken],
                'verify' => false,
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            return [
                'statusOptions' => $data[0]['document_status'] ?? [],
                'rejectionReasons' => $data[0]['rejection_reason'] ?? [],
            ];
        } catch (\Exception $e) {
            return ['statusOptions' => [], 'rejectionReasons' => []];
        }
    }

    public function updateDocumentStatus(Request $request)
    {
        if ($request->input('documentStatusId') == '213') {
            return redirect()->route('customers.rejectionReason', ['customerId' => $request->input('customerId')]);
        }

        $url = "{$this->apiBaseUrl}/customers/document/status";

        try {
            $this->client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'json' => [
                    'customerId' => $request->input('customerId'),
                    'documentStatusId' => $request->input('documentStatusId'),
                ],
            ]);

            return response()->json(['success' => true, 'message' => 'Document status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update document status']);
        }
    }

    public function saveRejectionReason(Request $request)
    {
        DB::beginTransaction(); // Start the transaction

        try {
            // Step 1: Update the document status via the API
            $url = "{$this->apiBaseUrl}/customers/document/status";
            $apiResponse = $this->client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'json' => [
                    'customerId' => $request->input('customerId'),
                    'documentStatusId' => '213',
                    'rejectedReasonId' => $request->input('rejectedReasonId'),
                ],
            ]);
            // Validate API response
            if ($apiResponse->getStatusCode() !== 200) {
                $responseBody = json_decode($apiResponse->getBody()->getContents(), true);
                $errorMessage = $responseBody['message'] ?? 'Unknown error';
                throw new \Exception('API Error: ' . $errorMessage);
            }

            // Step 2: Fetch customer data
            $customerData = $this->getCustomerDataById($request->input('customerId'));
            if (!$customerData) {
                throw new \Exception('Customer not found for ID: ' . $request->input('customerId'));
            }
            // Commit the transaction
            DB::commit();

            // Step 3: Send email only after transaction successfully commits
            DB::afterCommit(function () use ($customerData, $request) {
                $this->sendRejectionReasonEmail(
                    $customerData,
                    $request->input('rejectedReasonId'),
                    $request->input('note')
                );
            });
            return redirect()->route('customers.index')->with('success', 'Document status updated and rejection email sent successfully.');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on any error

            // Log the error for debugging
            Log::error('Failed to save rejection reason: ' . $e->getMessage());

            return back()->withErrors(['error' => 'Failed to update document status']);
        }
    }

    private function sendRejectionReasonEmail($customerData, $rejectedReasonId, $note)
    {
        $email = $customerData['email'] ?? 'default@example.com';
        $name = ($customerData['firstname'] ?? 'Unknown') . ' ' . ($customerData['lastname'] ?? 'User');
        $rejectionReasonLabel = $this->getRejectionReasonLabel($rejectedReasonId);

        Mail::to($email)->send(new RejectionReasonMail($name, $rejectionReasonLabel, $note ?? 'No additional notes provided'));
    }

    private function getRejectionReasonLabel($rejectedReasonId)
    {
        $rejectionReasons = $this->getDocumentStatusOptions()['rejectionReasons'];
        foreach ($rejectionReasons as $reason) {
            if ($reason['value'] == $rejectedReasonId) {
                return $reason['label'];
            }
        }
        return 'Unknown Reason';
    }

    private function getCustomerDataById($customerId)
    {
        $url = "{$this->apiBaseUrl}/customers/{$customerId}";

        try {
            $response = $this->client->request('GET', $url, [
                'headers' => ['Authorization' => 'Bearer ' . $this->apiToken],
                'verify' => false,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error("Failed to retrieve customer data: " . $e->getMessage());
            return ['firstname' => 'Default', 'lastname' => 'User', 'email' => 'default@example.com'];
        }
    }
}
