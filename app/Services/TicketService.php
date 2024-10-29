<?php

namespace App\Services;

use App\Http\Requests\TicketRequest;
use App\Mail\TicketAssigned;
use App\Mail\TicketResolved;
use App\Models\Configuration;
use App\Models\Ticket;
use App\Models\TicketPerformance;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TicketService
{
    public function getTickets($request)
    {
        try {
            $user = Auth::user();
            $filterStatus = $request->input('status');
            $filterAssigned = $request->input('assigned');
            $perPage = $request->input('per_page', 25);

            $ticketsQuery = Ticket::query();

            if ($user->role === 'user') {
                $ticketsQuery->where('assigned', $user->id);
            }

            if ($filterStatus) {
                $ticketsQuery->where('status', $filterStatus);
            }

            if ($filterAssigned) {
                $ticketsQuery->where('assigned', $filterAssigned);
            }

            // Fetch tickets and recalculate SLA for each
            $tickets = $ticketsQuery->paginate($perPage);
            foreach ($tickets as $ticket) {
                $this->recalculateSLA($ticket);
            }

            $users = User::where('role', 'user')->pluck('name', 'id');

            return view('tickets.index', compact('tickets', 'users', 'filterStatus', 'filterAssigned', 'perPage'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load tickets: ' . $e->getMessage());
        }
    }

    public function loadCreatePage()
    {
        try {
            $users = User::where('role', 'user')->pluck('name', 'id');
            $ticketId = 'TICKET-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            return view('tickets.create', compact('users', 'ticketId'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load ticket creation page: ' . $e->getMessage());
        }

    }

    public function storeTicket(TicketRequest $request)
    {
        try {
            DB::beginTransaction();

            $ticket = new Ticket();
            $ticket->description = $request->input('description');
            $ticket->section = $request->input('section');
            $ticket->email = $request->input('email');
            $ticket->status = $request->input('status');
            $ticket->assigned = $request->input('assigned');

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('attachments', $fileName, 'public');
                $ticket->attachment = $filePath;
            }

            $ticket->save();

            // Send email notification if ticket is assigned during creation
            if ($ticket->status === 'pending' && $ticket->assigned) {
                $assignedUser = User::find($ticket->assigned);
                if ($assignedUser) {
                    Mail::to($assignedUser->email)->send(new TicketAssigned($ticket, $assignedUser));
                }
            }

            // Create initial TicketPerformance record
            $this->createTicketPerformance($ticket);

            DB::commit();

            return redirect()->back()->with('success', 'Ticket created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create ticket: ' . $e->getMessage());
        }
    }

    public function updateTicket(TicketRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $ticket = Ticket::findOrFail($id);
            $user = Auth::user();
            $oldStatus = $ticket->status;
            $oldAssigned = $ticket->assigned;

            if ($user->role === 'admin' || $user->role === 'super admin') {
                $ticket->assigned = $request->input('assigned');
            }
            $ticket->status = $request->input('status');

            $ticket->save();

            if ($ticket->status === 'resolved' && $oldStatus !== 'resolved') {
                Mail::to($ticket->email)->send(new TicketResolved($ticket));
            }
            if ($ticket->status === 'pending' && $ticket->assigned) {
                $assignedUser = User::find($ticket->assigned);
                if ($assignedUser) {
                    Mail::to($assignedUser->email)->send(new TicketAssigned($ticket, $assignedUser));
                }
            }

            // Update TicketPerformance record
            $this->updateTicketPerformance($ticket, $oldStatus, $oldAssigned);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Ticket updated successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update ticket: ' . $e->getMessage()], 500);
        }
    }

    public function updateTicketIndex($request, $id)
    {
        try {
            DB::beginTransaction();

            $ticket = Ticket::findOrFail($id);
            $user = Auth::user();
            $oldStatus = $ticket->status;
            $oldAssigned = $ticket->assigned;

            // Check if no changes were made
            if ($oldStatus == $request->input('status') && $oldAssigned == $request->input('assigned')) {
                return response()->json(['success' => false, 'message' => 'No changes detected'], 200);
            }

            // Update only if the user is admin or super admin
            if ($user->role === 'admin' || $user->role === 'super admin') {
                $ticket->assigned = $request->input('assigned');
            }
            $ticket->status = $request->input('status');

            $ticket->save();

            if ($ticket->status === 'resolved' && $oldStatus !== 'resolved') {
                Mail::to($ticket->email)->send(new TicketResolved($ticket));
            }
            // Handle assignment notification
            if ($ticket->status === 'pending' && $ticket->assigned) {
                $assignedUser = User::find($ticket->assigned);
                if ($assignedUser) {
                    Mail::to($assignedUser->email)->send(new TicketAssigned($ticket, $assignedUser));
                }
            }

            // Update TicketPerformance record
            $this->updateTicketPerformance($ticket, $oldStatus, $oldAssigned);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Ticket updated successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Failed to update ticket: ' . $e->getMessage()], 500);
        }
    }

    public function downloadAttachment($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            if ($ticket->attachment) {
                $filePath = storage_path('app/public/' . $ticket->attachment);
                return response()->download($filePath, basename($ticket->attachment));
            }

            return response()->json(['error' => 'No attachment found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to download attachment: ' . $e->getMessage()], 500);
        }

    }

    public function showTicket($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $users = User::where('role', 'user')->pluck('name', 'id');
            return view('tickets.show', compact('ticket', 'users'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to load ticket details: ' . $e->getMessage());
        }

    }

    public function deleteTicket($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();
            return response()->json(['success' => 'Ticket deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete ticket: ' . $e->getMessage()], 500);
        }

    }
    /*
     ** API Controller Methods **
     */

    public function storeTicketAPI($request)
    {
        try {
            DB::beginTransaction();

            $email = $request->input('email');
            $minuteKey = 'ticket_request_limit_' . $email;
            $dailyKey = 'daily_ticket_requests_' . $email;

            if (Cache::has($minuteKey)) {
                return response()->json(['message' => 'You can only submit one ticket every minute.'], 429);
            }

            $dailyRequestCount = Cache::get($dailyKey, 0);
            if ($dailyRequestCount >= 5) {
                return response()->json(['message' => 'You have reached the daily limit of 5 tickets for this email.'], 429);
            }

            Cache::put($minuteKey, true, now()->addMinute());
            Cache::put($dailyKey, $dailyRequestCount + 1, now()->addDay());

            $ticket = new Ticket();
            $ticket->description = $request->input('description');
            $ticket->section = $request->input('section');
            $ticket->email = $email;
            $ticket->status = 'pending';
            $ticket->assigned = null;

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('attachments', $fileName, 'public');
                $ticket->attachment = $filePath;
            }

            $ticket->save();

            // Create initial TicketPerformance record
            $this->createTicketPerformance($ticket);

            DB::commit();

            return response()->json([
                'message' => 'Ticket created successfully.',
                'ticket' => $ticket,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create ticket.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    // New methods for SLA functionality

    private function createTicketPerformance(Ticket $ticket)
    {
        $performance = new TicketPerformance();
        $performance->ticket_id = $ticket->id;
        $performance->user_id = $ticket->assigned;
        $performance->user_name = $ticket->assigned ? User::find($ticket->assigned)->name : null;
        $performance->ticket_created_date = $ticket->created_at;
        $performance->assigned_date = $ticket->assigned ? now() : null;
        $performance->sla_status = 'in_sla';

        if ($ticket->assigned) {
            $performance->pending_date = now();
        }

        if ($ticket->status == 'in_progress') {
            $performance->in_progress_date = now();
        }

        $performance->save();
    }

    private function updateTicketPerformance(Ticket $ticket, $oldStatus, $oldAssigned)
    {
        $performance = TicketPerformance::where('ticket_id', $ticket->id)
            ->whereNull('resolved_date')
            ->latest()
            ->first();

        if (!$performance) {
            $this->createTicketPerformance($ticket);
            return;
        }

        $now = now();

        // If no open performance record exists or status is changing from resolved, create a new one
        if (!$performance || ($oldStatus == 'resolved' && $ticket->status != 'resolved')) {
            if ($performance) {
                $this->closeTicketPerformance($performance, $now);
            }
            $this->createTicketPerformance($ticket);
            return;
        }

        // Handle assignee changes
        if ($oldAssigned !== $ticket->assigned) {
            if ($oldAssigned === null) {
                // Ticket was unassigned and is now being assigned
                $performance->user_id = $ticket->assigned;
                $performance->user_name = User::find($ticket->assigned)->name;
                $performance->assigned_date = $now;
                if ($performance->pending_date === null) {
                    $performance->pending_date = $now;
                }
            } elseif ($ticket->assigned === null) {
                // Ticket is being unassigned, create a new record
                $this->closeTicketPerformance($performance, $now);
                $this->createTicketPerformance($ticket);
                return;
            } else {
                // Changing from one assigned user to another, create a new record
                $this->closeTicketPerformance($performance, $now);
                $this->createTicketPerformance($ticket);
                return;
            }
        }

        // Update status-related dates
        if ($ticket->status == 'resolved' && $oldStatus != 'resolved') {
            $performance->resolved_date = $now;
        } elseif ($oldStatus == 'resolved' && $ticket->status != 'resolved') {
            $performance->resolved_date = null;
        }

        // Handle in-progress date
        if ($ticket->status == 'in_progress' && $performance->in_progress_date === null) {
            $performance->in_progress_date = $now;
        }

        $this->calculateSLADuration($performance);
        $performance->save();
    }

    private function closeTicketPerformance(TicketPerformance $performance, $closeDate)
    {
        if ($performance->resolved_date === null) {
            $performance->resolved_date = $closeDate;
        }
        $this->calculateSLADuration($performance);
        $performance->save();
    }

    private function calculateSLADuration(TicketPerformance $performance)
    {
        $slaLimit = Configuration::getValueByKey('sla_limit');

        if ($performance->assigned_date) {
            $assignedDate = Carbon::parse($performance->assigned_date);
            // Use current time if the ticket is not resolved
            $endDate = $performance->resolved_date ? Carbon::parse($performance->resolved_date) : now();

            // Calculate SLA based on assigned date and end date
            $duration = $endDate->diffInHours($assignedDate);
            $performance->sla_duration = abs($duration);

            if ($slaLimit < abs($duration)) {
                $performance->sla_status = 'out_sla';
            } else {
                $performance->sla_status = 'in_sla';
            }

        } else {
            $performance->sla_duration = null;
            $performance->sla_status = null;
        }
    }

    private function recalculateSLA(Ticket $ticket)
    {
        $performance = TicketPerformance::where('ticket_id', $ticket->id)
            ->whereNull('resolved_date')
            ->latest()
            ->first();

        if ($performance) {
            $this->calculateSLADuration($performance);
        }
    }
}