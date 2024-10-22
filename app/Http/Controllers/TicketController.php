<?php

namespace App\Http\Controllers;

use App\Helpers\LogHelper;
use App\Http\Requests\TicketRequest;
use App\Models\TicketPerformance;
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
    public function showSlaTickets(Request $request)
    {
        // Default items per page (25, 50, 100)
        $perPage = $request->input('per_page', 25);

        // Filters from request (they may or may not be set)
        $userFilter = $request->input('user_name', '');
        $slaStatusFilter = $request->input('sla_status', '');
        $showReopenedOnly = $request->boolean('show_reopened', false);

        // Query builder for ticket performances
        $query = TicketPerformance::select(
            'id',
            'user_name',
            'ticket_id',
            'ticket_created_date',
            'assigned_date',
            'resolved_date',
            'sla_status'
        );

        // Apply user name filter if selected
        if (!empty($userFilter)) {
            if ($userFilter === 'unassigned') {
                $query->whereNull('user_name');
            } else {
                $query->where('user_name', $userFilter);
            }
        }

        // Apply SLA status filter if selected
        if (!empty($slaStatusFilter)) {
            if ($slaStatusFilter === 'out_sla') {
                // Consider tickets with explicit 'out_sla' status or null (unspecified status)
                $query->where(function ($subquery) {
                    $subquery->where('sla_status', 'out_sla')
                        ->orWhereNull('sla_status');
                });
            } else {
                // For 'in_sla' filtering, check explicitly
                $query->where('sla_status', $slaStatusFilter);
            }
        }

        // Add subquery to identify reopened tickets
        if ($showReopenedOnly) {
            $query->whereIn('ticket_id', function ($subquery) {
                $subquery->select('ticket_id')
                    ->from('ticket_performances')
                    ->groupBy('ticket_id')
                    ->havingRaw('COUNT(*) > 1');
            });
        }
        // Fetch repeated ticket IDs
        $repeatedTicketIds = TicketPerformance::select('ticket_id')
            ->groupBy('ticket_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('ticket_id')
            ->toArray();

        // Paginate results with configurable page size
        $slaTickets = $query->paginate($perPage)->withQueryString();

        // Fetch distinct user names for the filter dropdown, including empty values
        $users = TicketPerformance::distinct()
            ->whereNotNull('user_name')
            ->pluck('user_name')
            ->sort()
            ->values();

        // Return view with SLA tickets data and filters
        return view('tickets.sla', compact(
            'slaTickets',
            'users',
            'userFilter',
            'slaStatusFilter',
            'showReopenedOnly',
            'perPage',
            'repeatedTicketIds'
        ));
    }

}
