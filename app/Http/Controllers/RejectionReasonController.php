<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\StoreRejectionReasonRequest;
use App\Services\RejectionReasonService;

class RejectionReasonController extends Controller
{
    public function __construct(private RejectionReasonService $rejectionReasonService, private LogHelper $logHelper)
    {
        $this->rejectionReasonService = $rejectionReasonService;
        $this->logHelper = $logHelper;
    }

    public function index()
    {
        $this->logHelper->logAction('View Rejection Reasons', 'Fetching rejection reasons');
        $rejectionReasons = $this->rejectionReasonService->getRejectionReasons();

        if (empty($rejectionReasons)) {
            return back()->withErrors(['error' => 'Failed to fetch rejection reasons.']);
        }

        return view('rejection_reasons.index', compact('rejectionReasons'));
    }

    public function store(StoreRejectionReasonRequest $request)
    {
        $this->logHelper->logAction('Add Rejection Reason', 'Adding a new rejection reason');
        try {
            $this->rejectionReasonService->addRejectionReason($request->optionLabel);
            return redirect()->route('rejection-reasons.index')->with('success', 'Rejection reason added successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy($optionId)
    {
        $this->logHelper->logAction('Delete Rejection Reason', "Deleting rejection reason ID: {$optionId}");
        $success = $this->rejectionReasonService->removeRejectionReason($optionId);

        if ($success) {
            return redirect()->route('rejection-reasons.index')->with('success', 'Rejection reason deleted successfully.');
        }

        return back()->withErrors(['error' => 'Failed to delete rejection reason.']);
    }
}
