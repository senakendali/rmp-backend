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
    public function index(Request $request): JsonResponse
    {
        try {
            // Get the rnd_request_id from the query parameters or request data
            $rnd_request_id = $request->input('rnd_request_id'); 

            // Make sure rnd_request_id is provided
            if (!$rnd_request_id) {
                return response()->json(['error' => 'rnd_request_id is required'], 400);
            }

            // Fetch the product using the filtered rnd_request_id
            $product = RndProductDetail::where('rnd_request_id', $rnd_request_id)->first();

            // If product is not found, return a 404 error
            if (!$product) {
                return response()->json(['error' => 'Product not found'], 404);
            }

            // Return the product data as JSON with a 200 status code
            return response()->json($product, 200);
        } catch (Exception $e) {
            // Handle any other unexpected errors
            return response()->json(['error' => 'Failed to fetch product', 'message' => $e->getMessage()], 500);
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
