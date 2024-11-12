<?php

namespace App\Http\Controllers;

use App\Mail\RejectionReasonMail;
use App\Models\CustomerGroup;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $client = new Client();
        $perPage = $request->input('perPage', 25);
        $statusFilter = $request->input('document_status', null);

        $url = 'https://10.1.94.101/rest/V1/customers/search?searchCriteria=[]';
        $token = '5ffs6yf7snkspg1z99o7zhriubn92at8';

        $response = $client->request('GET', $url, [
            'headers' => ['Authorization' => 'Bearer ' . $token],
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
        $client = new Client();
        $url = "https://10.1.94.101/rest/V1/admin/customers/document/download?customerId=$customerId";
        $token = '5ffs6yf7snkspg1z99o7zhriubn92at8';

        try {
            $response = $client->request('GET', $url, [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'verify' => false,
                'stream' => true,
            ]);

            return response($response->getBody(), 200)
                ->header('Content-Type', 'application/zip')
                ->header('Content-Disposition', 'attachment; filename="document_CustomerID_' . $customerId . '.zip"');
        } catch (\Exception $e) {
            return response()->json(['error' => 'Document not found'], 404);
        }
    }
    public function getDocumentStatusOptions()
    {
        $client = new Client();
        $url = 'https://10.1.94.101/rest/V1/seoudi/customer/document-status-options';
        $token = '5ffs6yf7snkspg1z99o7zhriubn92at8';

        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'verify' => false,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            $statusOptions = $responseData[0]['document_status'] ?? [];
            $rejectionReasons = $responseData[0]['rejection_reason'] ?? [];
            //dd($rejectionReasons);
            return [
                'statusOptions' => $statusOptions,
                'rejectionReasons' => $rejectionReasons,
            ];

        } catch (\Exception $e) {
            return [
                'statusOptions' => [],
                'rejectionReasons' => [],
            ]; // Handle error by returning empty arrays
        }
    }
    public function updateDocumentStatus(Request $request)
    {
        $documentStatusId = $request->input('documentStatusId');

        if ($documentStatusId == '213') { // "Rejected" status
            // Redirect to rejection reason page without making an API call
            return redirect()->route('customers.rejectionReason', [
                'customerId' => $request->input('customerId'),
            ]);
        }

        // If not rejected, proceed with the update request
        $client = new Client();
        $url = 'https://10.1.94.101/rest/V1/customers/document/status';
        $token = '98bwbb5tv6v04xazs6mkb7diu3lxc3l7';

        try {
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'json' => [
                    'customerId' => $request->input('customerId'),
                    'documentStatusId' => $documentStatusId,
                ],
            ]);

            return response()->json(['success' => true, 'message' => 'Document status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update document status']);
        }
    }

    public function showRejectionReason($customerId)
    {
        // Fetch rejection reasons
        $statusOptions = $this->getDocumentStatusOptions();
        $rejectionReasons = $statusOptions['rejectionReasons'];

        return view('customers.rejection_reason', compact('customerId', 'rejectionReasons'));
    }

    public function saveRejectionReason(Request $request)
    {
        $client = new Client();
        $url = 'https://10.1.94.101/rest/V1/customers/document/status';
        $token = '98bwbb5tv6v04xazs6mkb7diu3lxc3l7';

        $request->validate([
            'rejectedReasonId' => 'required',
            'note' => 'required_if:rejectedReasonId,238', // Require note if "Other" is selected
        ]);

        try {
            // Update document status
            $response = $client->request('POST', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ],
                'verify' => false,
                'json' => [
                    'customerId' => $request->input('customerId'),
                    'documentStatusId' => '213', // Rejected status
                    'rejectedReasonId' => $request->input('rejectedReasonId'),
                ],
            ]);

            // Retrieve customer email and name for the email
            $customerData = $this->getCustomerDataById($request->input('customerId'));
            $customerEmail = $customerData['email'];
            $customerName = $customerData['firstname'] . ' ' . $customerData['lastname'];
            $rejectionReasonLabel = $this->getRejectionReasonLabel($request->input('rejectedReasonId'));
            $note = $request->input('note') ?? 'No additional notes provided';

            Mail::to($customerEmail)->send(new RejectionReasonMail($customerName, $rejectionReasonLabel, $note));

            return redirect()->route('customers.index')->with('success', 'Document status updated and rejection email sent successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(provider: ['error' => 'Failed to update document status']);
        }
    }

/**
 * Helper function to get the rejection reason label based on ID.
 */
    private function getRejectionReasonLabel($rejectedReasonId)
    {
        $statusOptions = $this->getDocumentStatusOptions();
        $rejectionReasons = $statusOptions['rejectionReasons'];

        foreach ($rejectionReasons as $reason) {
            if ($reason['value'] == $rejectedReasonId) {
                return $reason['label'];
            }
        }
        return 'Unknown Reason';
    }
    private function getCustomerDataById($customerId)
    {
        $client = new Client();
        $url = "https://10.1.94.101/rest/V1/customers/{$customerId}";
        $token = '5ffs6yf7snkspg1z99o7zhriubn92at8';

        try {
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                ],
                'verify' => false,
            ]);

            $customerData = json_decode($response->getBody()->getContents(), true);
            $customerData['firstname'] = $customerData['firstname'] ?? 'Default Firstname';
            $customerData['lastname'] = $customerData['lastname'] ?? 'Default Lastname';
            $customerData['email'] = $customerData['email'] ?? 'default@example.com';

            return $customerData;

        } catch (\Exception $e) {
            Log::error("Failed to retrieve customer data for ID {$customerId}: " . $e->getMessage());
            return [
                'firstname' => 'Default Firstname',
                'lastname' => 'Default Lastname',
                'email' => 'default@example.com',
            ];
        }
    }
}
