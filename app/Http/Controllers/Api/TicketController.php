<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use App\Services\TicketService;

class TicketController extends Controller
{
    public function __construct(private TicketService $ticketService)
    {
    }

    public function store(TicketRequest $request)
    {
        return $this->ticketService->storeTicketAPI($request);
    }
}
