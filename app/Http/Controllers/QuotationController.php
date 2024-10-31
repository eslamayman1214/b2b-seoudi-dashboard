<?php

namespace App\Http\Controllers;

use App\Mail\QuotationReplyMail;
use App\Models\Quotation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $perPage = $request->input('perPage', 25);

        $quotations = Quotation::when($status, function ($query, $status) {
            return $query->where('last_status', $status);
        })
            ->paginate($perPage);

        return view('quotations.index', compact('quotations'));
    }

    public function reply($id)
    {
        $quotation = Quotation::findOrFail($id);
        return view('quotations.reply', compact('quotation'));
    }

    public function sendReply(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $quotation = Quotation::findOrFail($id);
        $quotation->last_status = $request->status;
        $quotation->save();

        // Send email to quotation email address
        Mail::to($quotation->email)->send(new QuotationReplyMail($quotation, $request->status, $request->notes));

        return redirect()->route('quotations.index')->with('success', 'Reply sent successfully.');
    }
}