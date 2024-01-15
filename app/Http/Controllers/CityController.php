<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Citys;

class CityController extends Controller

{
    public function get()
    {
        Log::info("Searching all citys");
        $citys = Citys::all();
        return response()->json([
            "data" => $citys
        ], 200);
    }
}
