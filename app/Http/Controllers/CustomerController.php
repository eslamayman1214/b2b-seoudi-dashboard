<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\SaveRejectionReasonRequest;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct(private CustomerService $customerService, private LogHelper $logHelper)
    {
        $this->customerService = $customerService;
        $this->logHelper = $logHelper;
    }

    public function index(Request $request)
    {
        $this->logHelper->logAction('View Customers', 'Loading customer index');
        return $this->customerService->getCustomers($request);
    }

    public function downloadDocument($customerId)
    {
        $this->logHelper->logAction('Download Document', "Downloading document for customer ID: {$customerId}");
        return $this->customerService->downloadDocument($customerId);
    }

    public function getDocumentStatusOptions()
    {
        $this->logHelper->logAction('Get Document Status Options', 'Fetching document status options');
        return $this->customerService->getDocumentStatusOptions();
    }

    public function updateDocumentStatus(Request $request)
    {
        $customerId = $request->input('customerId');
        $documentStatusId = $request->input('documentStatusId');
        $this->logHelper->logAction('Update Document Status', "Updating document status for customer ID: {$customerId}, status: {$documentStatusId}");

        return $this->customerService->updateDocumentStatus($request);
    }

    public function showRejectionReason($customerId)
    {
        $this->logHelper->logAction('Show Rejection Reason', "Displaying rejection reasons for customer ID: {$customerId}");
        return view('customers.rejection_reason', [
            'customerId' => $customerId,
            'rejectionReasons' => $this->customerService->getDocumentStatusOptions()['rejectionReasons'],
        ]);
    }

    public function saveRejectionReason(SaveRejectionReasonRequest $request)
    {
        $customerId = $request->input('customerId');
        $rejectedReasonId = $request->input('rejectedReasonId');

        // Log the action
        $this->logHelper->logAction('Save Rejection Reason', "Saving rejection reason ID: {$rejectedReasonId} for customer ID: {$customerId}");

        // Pass the request to the service
        return $this->customerService->saveRejectionReason($request);
    }
}
