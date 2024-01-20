<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\State;

class StateController extends Controller
{
    public function get()
    {
        Log::info("Searching all states");
        $states = State::all();
        return response()->json([
            "data" => $states
        ], 200);
    }
}
