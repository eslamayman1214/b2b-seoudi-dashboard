<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;

class TicketController extends Controller
{
    public function store(TicketRequest $request)
    {
        try {
            $email = $request->input('email');
            $minuteKey = 'ticket_request_limit_' . $email;
            $dailyKey = 'daily_ticket_requests_' . $email;

            // Check if user has sent a request within the last minute
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
            // Handle exceptions and return error response
            return response()->json([
                'error' => 'Failed to create ticket.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
