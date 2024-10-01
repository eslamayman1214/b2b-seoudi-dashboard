<?php

namespace App\Services;

use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

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

            $tickets = $ticketsQuery->paginate($perPage);
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
            $ticket = new Ticket();
            $ticket->description = $request->input('description');
            $ticket->department = $request->input('department');
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

            return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to create ticket: ' . $e->getMessage());
        }
    }

    public function updateTicket(TicketRequest $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $user = Auth::user();

            if ($user->role === 'admin') {
                $ticket->status = $request->input('status');
                $ticket->assigned = $request->input('assigned');
            } elseif ($user->role === 'user') {
                $ticket->status = $request->input('status');
                if ($ticket->assigned !== $user->id) {
                    $ticket->assigned = $user->id;
                }
            }

            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('attachments', $fileName, 'public');
                $ticket->attachment = $filePath;
            }

            $ticket->save();

            return response()->json(['success' => true, 'message' => 'Ticket updated successfully']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update ticket: ' . $e->getMessage()], 500);
        }
    }

    public function updateTicketIndex($request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->status = $request->input('status');
            $ticket->assigned = $request->input('assigned');
            $ticket->save();

            return response()->json(['success' => true, 'message' => 'Ticket updated successfully']);
        } catch (Exception $e) {
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
            $email = $request->input('email');
            $minuteKey = 'ticket_request_limit_' . $email;
            $dailyKey = 'daily_ticket_requests_' . $email;

            // Check if the user has sent a request within the last minute
            if (Cache::has($minuteKey)) {
                return response()->json(['message' => 'You can only submit one ticket every minute.'], 429);
            }

            // Check how many requests have been made today
            $dailyRequestCount = Cache::get($dailyKey, 0);
            if ($dailyRequestCount >= 5) {
                return response()->json(['message' => 'You have reached the daily limit of 5 tickets for this email.'], 429);
            }

            // Increment daily request count and set minute cooldown
            Cache::put($minuteKey, true, now()->addMinute()); // 1 minute limit
            Cache::put($dailyKey, $dailyRequestCount + 1, now()->addDay()); // Daily limit

            // Create a new ticket with default status as 'pending' and assigned as null
            $ticket = new Ticket();
            $ticket->description = $request->input('description');
            $ticket->department = $request->input('department');
            $ticket->email = $email;
            $ticket->status = 'pending'; // Always store as 'pending'
            $ticket->assigned = null; // Always store as null

            // Handle file upload if an attachment is provided
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('attachments', $fileName, 'public');
                $ticket->attachment = $filePath;
            }

            // Save the ticket
            $ticket->save();

            // Return JSON response with the newly created ticket
            return response()->json([
                'message' => 'Ticket created successfully.',
                'ticket' => $ticket,
            ], 201);
        } catch (\Exception $e) {
            // Log the error if you have a logging service (optional)
            // $this->logService->logError('Error creating ticket via API', $e->getMessage());

            // Handle exceptions and return error response
            return response()->json([
                'error' => 'Failed to create ticket.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
