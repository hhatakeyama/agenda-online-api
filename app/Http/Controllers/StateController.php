<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\States;

class StateController extends Controller
{
    public function get()
    {
        Log::info("Searching all states");
        $states = States::all();
        return response()->json([
            "data" => $states
        ], 200);
    }
}
