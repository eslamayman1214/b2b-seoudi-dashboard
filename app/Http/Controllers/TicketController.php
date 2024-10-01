<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\TicketRequest;
use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(private TicketService $ticketService, private LogHelper $logService)
    {
    }

    public function index(Request $request)
    {
        $this->logService->logAction('View Ticket List', 'Fetching ticket list');
        return $this->ticketService->getTickets($request);
    }

    public function create()
    {
        $this->logService->logAction('Create Ticket Page', 'Loading ticket creation page');
        return $this->ticketService->loadCreatePage();
    }

    public function store(TicketRequest $request)
    {
        $this->logService->logAction('Store Ticket', 'Creating a new ticket');
        return $this->ticketService->storeTicket($request);
    }

    public function update(TicketRequest $request, $id)
    {
        $this->logService->logAction('Update Ticket', "Updating ticket ID: {$id}");
        return $this->ticketService->updateTicket($request, $id);
    }

    public function update_index(Request $request, $id)
    {
        $this->logService->logAction('Update Ticket Index', "Updating ticket index ID: {$id}");
        return $this->ticketService->updateTicketIndex($request, $id);
    }

    public function downloadAttachment($id)
    {
        $this->logService->logAction('Download Attachment', "Downloading attachment for ticket ID: {$id}");
        return $this->ticketService->downloadAttachment($id);
    }

    public function show($id)
    {
        $this->logService->logAction('Show Ticket', "Fetching details for ticket ID: {$id}");
        return $this->ticketService->showTicket($id);
    }

    public function destroy($id)
    {
        $this->logService->logAction('Delete Ticket', "Deleting ticket ID: {$id}");
        return $this->ticketService->deleteTicket($id);
    }
}
