<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MeasurementUnit;

class MeasurementUnitController extends Controller
{
    // Get all measurement units
    public function index()
    {
        $units = MeasurementUnit::all();
        return response()->json($units);
    }

    // Get a single measurement unit by ID
    public function show($id)
    {
        $unit = MeasurementUnit::find($id);

        if (!$unit) {
            return response()->json(['message' => 'Measurement unit not found'], 404);
        }

        return response()->json($unit);
    }

    // Create a new measurement unit
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'abbreviation' => 'required|string|max:50',
        ]);

        $unit = MeasurementUnit::create($validated);

        return response()->json($unit, 201);
    }

    // Update an existing measurement unit
    public function update(Request $request, $id)
    {
        $unit = MeasurementUnit::find($id);

        if (!$unit) {
            return response()->json(['message' => 'Measurement unit not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'abbreviation' => 'sometimes|string|max:50',
        ]);

        $unit->update($validated);

        return response()->json($unit);
    }

    // Delete a measurement unit
    public function destroy($id)
    {
        $unit = MeasurementUnit::find($id);

        if (!$unit) {
            return response()->json(['message' => 'Measurement unit not found'], 404);
        }

        $unit->delete();

        return response()->json(['message' => 'Measurement unit deleted successfully'], 200);
    }
}
