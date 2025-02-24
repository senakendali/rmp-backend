<?php

namespace App\Http\Controllers;

use App\Models\RndProductDetail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class RndProductDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $products = RndProductDetail::paginate(10); // Menampilkan 10 data per halaman
            return response()->json($products, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch data',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'rnd_request_id' => 'required|exists:rnd_requests,id',
                'name' => 'required|array',
                'manufacturer' => 'required|string',
                'registrant' => 'required|string',
            ]);

            $validated['name'] = json_encode($validated['name']); // Simpan sebagai JSON

            $product = RndProductDetail::create($validated);

            return response()->json($product, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create product', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $product = RndProductDetail::findOrFail($id);
            return response()->json($product, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch product', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $product = RndProductDetail::findOrFail($id);

            $validated = $request->validate([
                'rnd_request_id' => 'sometimes|exists:rnd_requests,id',
                'name' => 'sometimes|array',
                'product_substance_id' => 'sometimes|integer',
                'manufacturer' => 'sometimes|string',
                'registrant' => 'sometimes|string',
            ]);

            if (isset($validated['name'])) {
                $validated['name'] = json_encode($validated['name']); // Simpan sebagai JSON
            }

            $product->update($validated);

            return response()->json($product, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update product', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $product = RndProductDetail::findOrFail($id);
            $product->delete();

            return response()->json(['message' => 'Product deleted successfully'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Product not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete product', 'message' => $e->getMessage()], 500);
        }
    }
}
