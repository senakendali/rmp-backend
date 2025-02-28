<?php

namespace App\Http\Controllers;

use App\Models\RndProductSubstance;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RndProductSubstanceController extends Controller
{
    public function index()
    {
        

        try {
            $substances = RndProductSubstance::all();  // Ensure the correct model casing
            return response()->json($substances);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        $request->validate([
            'rnd_request_id' => 'required|exists:rnd_requests,id',  // Fixed the typo
            'active_substance' => 'required|string',
            'strength' => 'required|string',
            'dose' => 'required|string',
            'form' => 'required|string',
            'packaging' => 'required|string',
            'brand' => 'required|string',
            'hna_target' => 'required|numeric'
        ]);

        $substance = RndProductSubstance::create($request->all());  // Fixed the typo

        // Return the created substance as JSON with a 201 status code
        return response()->json($substance, 201);
    }

    // Display the specified resource
    public function show(RndProductSubstance $rndProductSubstance)
    {
        return response()->json($rndProductSubstance);
    }

    // Update the specified resource in storage
    public function update(Request $request, RndProductSubstance $rndProductSubstance)
    {
        $request->validate([
            'rnd_request_id' => 'exists:rnd_requests,id',  // Fixed the typo
            'active_substance' => 'string',
            'strength' => 'string',
            'dose' => 'string',
            'form' => 'string',
            'packaging' => 'string',
            'brand' => 'string',
            'hna_target' => 'numeric'
        ]);

        $rndProductSubstance->update($request->all());

        return response()->json($rndProductSubstance);
    }

    // Remove the specified resource from storage
    public function destroy(RndProductSubstance $rndProductSubstance)
    {
        $rndProductSubstance->delete();

        return response()->json(null, 204);  // Return empty response with 204 status code
    }
}
