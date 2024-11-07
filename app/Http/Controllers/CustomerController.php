<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

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

            $statusOptions = json_decode($response->getBody()->getContents(), true);
            return $statusOptions;

        } catch (\Exception $e) {
            return []; // Handle error by returning an empty array or logging the error
        }
    }
    public function updateDocumentStatus(Request $request)
    {
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
                    'documentStatusId' => $request->input('documentStatusId'),
                ],
            ]);

            return response()->json(['success' => true, 'message' => 'Document status updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update document status']);
        }
    }

}
