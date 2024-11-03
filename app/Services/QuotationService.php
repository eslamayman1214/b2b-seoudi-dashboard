<?php

namespace App\Services;

use App\Mail\QuotationReceived;
use App\Mail\QuotationReplyMail;
use App\Models\Quotation;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class QuotationService
{
    public function listQuotations($request)
    {
        try {
            $status = $request->input('status');
            $perPage = $request->input('perPage', 25);

            $quotations = Quotation::when($status, function ($query, $status) {
                return $query->where('last_status', $status);
            })->paginate($perPage);

            return view('quotations.index', compact('quotations'));
        } catch (\Exception $e) {
            Log::error('Failed to list quotations: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load quotations.');
        }
    }

    public function getQuotationForReply($id)
    {
        try {
            $quotation = Quotation::findOrFail($id);
            return view('quotations.reply', compact('quotation'));
        } catch (\Exception $e) {
            Log::error("Failed to retrieve quotation for reply (ID: $id): " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load quotation for reply.');
        }
    }

    public function sendQuotationReply($request, $id)
    {
        try {
            $quotation = Quotation::findOrFail($id);
            $quotation->last_status = $request->status;
            $quotation->save();

            // Send email to the quotation email address
            Mail::to($quotation->email)->send(new QuotationReplyMail($quotation, $request->status, $request->notes));

            return redirect()->route('quotations.index')->with('success', 'Reply sent successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to send quotation reply: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to send reply.');
        }
    }

    public function storeQuotationAPI($request)
    {
        try {
            DB::beginTransaction();

            // Create new Quotation
            $quotation = Quotation::create([
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'phone' => $request->input('phone'),
                'company_name' => $request->input('company_name'),
                'description' => $request->input('description'),
                'quantity_required' => $request->input('quantity'),
                'desired_delivery_date' => $request->input('delivery_date'),
                'additional_notes' => $request->input('notes'),
                //'status' => 'pending',
            ]);

            // Send confirmation email to user
            Mail::to($quotation->email)->send(new QuotationReceived($quotation));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Quotation submitted successfully. A confirmation email has been sent to you.',
                'quotation' => $quotation,
            ], 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to store quotation: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit quotation. Error: ' . $e->getMessage(), // Including specific error
            ], 500);
        }
    }
}