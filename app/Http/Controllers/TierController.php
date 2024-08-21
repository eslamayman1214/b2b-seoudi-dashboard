<?php

namespace App\Http\Controllers;

use App\Models\Tier;

class TierController extends Controller
{
    public function destroy($id)
    {
        try {
            $tier = Tier::findOrFail($id);
            $tier->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}