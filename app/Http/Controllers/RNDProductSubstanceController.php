<?php

namespace App\Http\Controllers;

use App\Models\RndProductSubstance;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Exception;

class RndProductSubstanceController extends Controller
{
    // Get all data with pagination
    public function index(Request $request): JsonResponse
    {
        try {
            // Ambil query parameter rnd_product_details_id jika ada
            $query = RndProductSubstance::query();

            if ($request->has('rnd_product_details_id')) {
                $query->where('rnd_product_details_id', $request->rnd_product_details_id);
            }

            $substances = $query->paginate(10);

            return response()->json($substances, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch data',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    // Get single data
    public function show($id): JsonResponse
    {
        try {
            $substance = RndProductSubstance::findOrFail($id);
            return response()->json($substance, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Data not found',
                'message' => $e->getMessage()
            ], 404);
        }
    }

    // Store new data
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'rnd_product_details_id' => 'required|exists:rnd_product_details,id',
            'active_substance' => 'required|string',
            'strength' => 'required|string',
            'dose' => 'required|string',
            'form' => 'required|string',
            'packaging' => 'required|string',
            'brand' => 'required|string',
            'hna_target' => 'required|numeric',
        ]);

        try {
            $substance = RndProductSubstance::create($request->all());
            return response()->json($substance, 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to create data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Update data
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'rnd_product_details_id' => 'sometimes|exists:rnd_product_details,id',
            'active_substance' => 'sometimes|string',
            'strength' => 'sometimes|string',
            'dose' => 'sometimes|string',
            'form' => 'sometimes|string',
            'packaging' => 'sometimes|string',
            'brand' => 'sometimes|string',
            'hna_target' => 'sometimes|numeric',
        ]);

        try {
            $substance = RndProductSubstance::findOrFail($id);
            $substance->update($request->all());
            return response()->json($substance, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to update data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Delete data
    public function destroy($id): JsonResponse
    {
        try {
            $substance = RndProductSubstance::findOrFail($id);
            $substance->delete();
            return response()->json(['message' => 'Data deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to delete data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
