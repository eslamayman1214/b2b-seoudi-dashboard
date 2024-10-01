<?php

namespace App\Http\Controllers;

use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load tickets: ' . $e->getMessage());
        }
    }

    public function create()
    {
        try {
            $users = User::where('role', 'user')->pluck('name', 'id');
            $ticketId = 'TICKET-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            return view('tickets.create', compact('users', 'ticketId'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load ticket creation page: ' . $e->getMessage());
        }
    }

    public function store(TicketRequest $request)
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
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to create ticket: ' . $e->getMessage());
        }
    }

    public function update(TicketRequest $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

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

            return response()->json(['success' => true, 'message' => 'Ticket updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update ticket: ' . $e->getMessage()], 500);
        }
    }

    public function update_index(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->status = $request->input('status');
            $ticket->assigned = $request->input('assigned');
            $ticket->save();

            return response()->json(['success' => true, 'message' => 'Ticket updated successfully']);
        } catch (\Exception $e) {
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
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to download attachment: ' . $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $users = User::where('role', 'user')->pluck('name', 'id');
            return view('tickets.show', compact('ticket', 'users'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to load ticket details: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $ticket = Ticket::findOrFail($id);
            $ticket->delete();
            return response()->json(['success' => 'Ticket deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete ticket: ' . $e->getMessage()], 500);
        }
    }
}
