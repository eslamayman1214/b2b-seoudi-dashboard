<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\QuotationRequest;
use App\Mail\QuotationReceived;
use App\Models\Quotation;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class QuotationController extends Controller
{
    /**
     * Store a new quotation and send confirmation email.
     */
    public function store(QuotationRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create new Quotation
            $quotation = new Quotation();
            $quotation->name = $request->input('name');
            $quotation->email = $request->input('email');
            $quotation->phone = $request->input('phone');
            $quotation->company_name = $request->input('company_name');
            $quotation->description = $request->input('description');
            $quotation->quantity_required = $request->input('quantity');
            $quotation->desired_delivery_date = $request->input('delivery_date');
            $quotation->additional_notes = $request->input('notes');
            //$quotation->status = 'pending';
            $quotation->save();

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
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit quotation: ' . $e->getMessage(),
            ], 500);
        }
    }
}