<?php

namespace App\Http\Controllers;
use App\Models\GoodsCategories;
use Illuminate\Http\Request;

class GoodsCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(GoodsCategories::paginate(10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'goods_type' => 'required|in:material,non-material',
            ]);
    
            // Create a new goods category
            $goods_category = GoodsCategories::create($validated);
    
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Goods category created successfully.',
                'data' => $goods_category
            ], 201);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation failure response
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            // Return general error response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the goods category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(GoodsCategories $goods_category)
    {
        return response()->json($goods_category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'goods_type' => 'required|in:material,non-material',
                'status' => 'required|in:active,inactive',
            ]);

            // Find the goods category by ID
            $goods_category = GoodsCategories::find($id);
    
            // If the goods category is not found, return a failure response
            if (!$goods_category) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Goods category not found.',
                ], 404);
            }
    
            // Validate the incoming request
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|string|max:255',
            ]);
    
            // Update the goods category
            $goods_category->update($validated);
    
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Goods category updated successfully.',
                'data' => $goods_category
            ], 200);
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation failure response
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation error.',
                'errors' => $e->errors()
            ], 422);
    
        } catch (\Exception $e) {
            // Return general error response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the goods category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GoodsCategories $goods_category)
    {
        try {
            // Find the goods category by ID
            $goods_category = GoodsCategories::find($id);
    
            // If the goods category is not found, return a failure response
            if (!$goods_category) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Goods category not found.',
                ], 404);
            }
    
            // Delete the goods category
            $goods_category->delete();
    
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Goods category deleted successfully.',
            ], 200);
    
        } catch (\Exception $e) {
            // Return error response for any unforeseen issues
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the goods category.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
