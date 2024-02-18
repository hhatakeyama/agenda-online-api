<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Organization;
use App\Models\Company;

class OrganizationController extends Controller
{
    public function getCompaniesFromOrganization($slug)
    {
        try {
            Log::info("Searching companies from organization id", [$slug]);
            $organization = Organization::where('slug', $slug)->firstOrFail();
            $organization->companies = Company::where('organization_id', $organization->id)->where('status', 1)->select('id', 'name')->get();
            return response()->json([
                "data" => $organization
            ], 200);
        } catch(\Exception $e) {
            Log::error("Error searching companies from organization", [$slug]);
            return response()->json([
                "message" => "Empresa não encontrada.",
            ], 400);
        }
    }
}
