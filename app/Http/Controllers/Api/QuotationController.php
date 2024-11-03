<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationRequest;
use App\Services\QuotationService;

class QuotationController extends Controller
{
    public function __construct(private QuotationService $quotationService)
    {
    }

    public function store(QuotationRequest $request)
    {
        return $this->quotationService->storeQuotationAPI($request);
    }
}