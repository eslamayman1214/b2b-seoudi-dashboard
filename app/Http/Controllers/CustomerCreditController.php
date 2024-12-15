<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\CreateCustomerCreditRequest;
use App\Http\Requests\UpdateCustomerCreditRequest;
use App\Services\CustomerCreditService;

class CustomerCreditController extends Controller
{
    protected $customerCreditService;
    protected $logHelper;

    public function __construct(CustomerCreditService $customerCreditService, LogHelper $logHelper)
    {
        $this->customerCreditService = $customerCreditService;
        $this->logHelper = $logHelper;
    }

    public function getCustomerCredit($customerId)
    {
        $this->logHelper->logAction('Get Customer Credit', "Fetching credit details for customer ID: {$customerId}");

        $creditData = $this->customerCreditService->getCustomerCredit($customerId);

        if (is_null($creditData)) {
            return redirect()->route('customer.credit.create', ['customerId' => $customerId]);
        }

        return view('customers.credit', ['creditData' => $creditData]);
    }

    public function showCreateCreditForm($customerId)
    {
        return view('customers.create_credit', ['customerId' => $customerId]);
    }

    public function createCustomerCredit(CreateCustomerCreditRequest $request)
    {
        $data = $request->validated();
        $data['credit_due_days'] = 10;
        $data['status'] = 1;

        $this->logHelper->logAction('Create Customer Credit', "Creating credit for customer ID: {$data['customer_id']}");

        if ($this->customerCreditService->createCustomerCredit($data)) {
            return redirect()->route('customer.credit', ['customerId' => $data['customer_id']])
                ->with('success', 'Customer credit created successfully.');
        }

        return back()->withErrors('Failed to create customer credit.');
    }

    public function updateCustomerCredit(UpdateCustomerCreditRequest $request, $customerId)
    {
        $data = $request->validated();
        $data['customer_id'] = $customerId;

        $this->logHelper->logAction('Update Customer Credit', "Updating credit for customer ID: {$customerId}");

        if ($this->customerCreditService->updateCustomerCredit($data)) {
            return back()->with('success', 'Customer credit updated successfully!');
        }

        return back()->withErrors('Failed to update customer credit.');
    }
}
