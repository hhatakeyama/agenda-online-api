<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\City;

class CityController extends Controller

{
    public function get()
    {
        Log::info("Searching all cities");
        $cities = City::all();
        return response()->json([
            "data" => $cities
        ], 200);
    }

    public function getByStateId($state_id)
    {
        Log::info("Searching all cities");
        $cities = City::where("state_id", $state_id)->get();
        return response()->json([
            "data" => $cities
        ], 200);
    }
}
