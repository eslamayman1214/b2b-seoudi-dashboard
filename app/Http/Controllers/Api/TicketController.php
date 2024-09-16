<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'description' => 'required|string|max:1000', // Increased max length for description
            'department' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Optional file validation
            'status' => 'required|in:pending,in progress,resolved',
            'assigned' => 'nullable|exists:users,id', // Ensure assigned user ID exists
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Create a new ticket
        $ticket = new Ticket();
        $ticket->description = $request->input('description');
        $ticket->department = $request->input('department');
        $ticket->email = $request->input('email');
        $ticket->status = $request->input('status');
        $ticket->assigned = $request->input('assigned');

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
    }
}