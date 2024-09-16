<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        // Get the current authenticated user
        $user = Auth::user();

        // Get the filter status and assigned (assignee) from the request
        $filterStatus = $request->input('status');
        $filterAssigned = $request->input('assigned'); // New filter

        // Get the number of items per page (default is 25)
        $perPage = $request->input('per_page', 25);

        // Fetch tickets with optional status and assigned filters
        $ticketsQuery = Ticket::query();

        // If user is not a super admin or admin, filter the tickets assigned to them
        if ($user->role === 'user') {
            $ticketsQuery->where('assigned', $user->id);
        }

        // Apply status filter if it's present
        if ($filterStatus) {
            $ticketsQuery->where('status', $filterStatus);
        }

        // Apply assigned filter if it's present (new filter)
        if ($filterAssigned) {
            $ticketsQuery->where('assigned', $filterAssigned);
        }

        // Apply pagination
        $tickets = $ticketsQuery->paginate($perPage);

        // Get all users with the 'user' role (for the assignee dropdown)
        $users = User::where('role', 'user')->pluck('name', 'id');

        // Pass data to the view
        return view('tickets.index', compact('tickets', 'users', 'filterStatus', 'filterAssigned', 'perPage'));
    }

    public function create()
    {
        // Fetch all users with role 'user'
        $users = User::where('role', 'user')->pluck('name', 'id');

        // Generate a new unique ticket ID
        $ticketId = 'TICKET-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        return view('tickets.create', compact('users', 'ticketId'));
    }

    // Store a newly created ticket in the database
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'description' => 'required|string|max:1000', // Increased max length for description
            'department' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Optional file validation
            'status' => 'required|in:pending,in progress,resolved',
            'assigned' => 'nullable|exists:users,id', // Ensure assigned user ID exists
        ]);

        // Create a new ticket
        $ticket = new Ticket();
        $ticket->description = $request->input('description');
        $ticket->department = $request->input('department');
        $ticket->email = $request->input('email');
        $ticket->status = $request->input('status');
        $ticket->assigned = $request->input('assigned');

        // Handle file upload if a new attachment is provided
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('attachments', $fileName, 'public');
            $ticket->attachment = $filePath;
        }

        // Save the ticket
        $ticket->save();

        // Redirect to tickets index with a success message
        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully.');
    }

    public function update(Request $request, $id)
    {
        try {
            $ticket = Ticket::findOrFail($id);

            // Validate the request data
            $request->validate([
                'description' => 'required|string',
                'department' => 'required|string',
                'email' => 'required|email',
                'status' => 'required|in:pending,in progress,resolved',
                'assigned' => 'nullable|exists:users,id',
                'attachment' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            ]);

            // Update ticket fields
            $ticket->description = $request->input('description');
            $ticket->department = $request->input('department');
            $ticket->email = $request->input('email');
            $ticket->status = $request->input('status');
            $ticket->assigned = $request->input('assigned');

            // Handle file upload if a new attachment is provided
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('attachments', $fileName, 'public');
                $ticket->attachment = $filePath;
            }

            // Save the ticket
            $ticket->save();

            // Return a success response
            return response()->json(['success' => true, 'message' => 'Ticket updated successfully']);
        } catch (\Exception $e) {
            // Return an error response if something goes wrong
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function update_index(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->status = $request->status;
        $ticket->assigned = $request->assigned;
        $ticket->save();

        return response()->json(['success' => true]);
    }

    public function downloadAttachment($id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->attachment) {
            $filePath = storage_path('app/public/' . $ticket->attachment);
            return response()->download($filePath, basename($ticket->attachment));
        }

        return response()->json(['error' => 'No attachment found'], 404);
    }

    public function show($id)
    {
        // Retrieve the ticket by ID
        $ticket = Ticket::findOrFail($id);

        // Get users with the role 'user'
        $users = User::where('role', 'user')->pluck('name', 'id');

        // Pass the ticket, users, and any other necessary data to the view
        return view('tickets.show', compact('ticket', 'users'));
    }

    public function destroy($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404); // If ticket doesn't exist
        }

        $ticket->delete();

        return response()->json(['success' => 'Ticket deleted successfully']);
    }

}