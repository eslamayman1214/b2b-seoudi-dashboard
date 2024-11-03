<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\QuotationReplyRequest;
use App\Services\QuotationService;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
    public function __construct(private QuotationService $quotationService, private LogHelper $logService)
    {
    }
    public function index(Request $request)
    {
        // Log action
        $this->logService->logAction('View Quotations', 'Fetching list of quotations');
        return $this->quotationService->listQuotations($request);
    }

    public function reply($id)
    {
        // Log action
        $this->logService->logAction('Reply to Quotation', "Viewing reply form for quotation ID: $id");
        return $this->quotationService->getQuotationForReply($id);
    }

    public function sendReply(QuotationReplyRequest $request, $id)
    {
        // Log action
        $this->logService->logAction('Send Quotation Reply', "Sending reply for quotation ID: $id");
        return $this->quotationService->sendQuotationReply($request, $id);
    }
}