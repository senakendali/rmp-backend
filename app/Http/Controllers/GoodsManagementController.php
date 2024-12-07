<?php

namespace App\Http\Controllers;

use App\Models\Goods;
use App\Models\GoodsCategories;
use Illuminate\Http\Request;

class GoodsManagementController extends Controller
{
    /**
     * Display a listing of the goods.
     */
    public function index()
    {
        try {
            // Paginate the goods
            $goods = Goods::with('category')->paginate(10);
            return response()->json($goods);
        } catch (\Exception $e) {
            // Return general error response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching goods.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created good in storage.
     */
    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'goods_category_id' => 'required|exists:goods_category,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'measurement' => 'required|string|max:255',
            ]);
    
            // Create a new good
            $good = Goods::create($validated);
    
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Good created successfully.',
                'data' => $good
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
                'message' => 'An error occurred while creating the good.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified good.
     */
    public function show(Goods $good)
    {
        try {
            return response()->json($good);
        } catch (\Exception $e) {
            // Return error response for any unforeseen issues
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the good.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified good in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'goods_category_id' => 'required|exists:goods_category,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'measurement' => 'required|string|max:255',
            ]);
    
            // Find the good by ID
            $good = Goods::find($id);
    
            // If the good is not found, return a failure response
            if (!$good) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Good not found.',
                ], 404);
            }
    
            // Update the good
            $good->update($validated);
    
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Good updated successfully.',
                'data' => $good
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
                'message' => 'An error occurred while updating the good.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified good from storage.
     */
    public function destroy(string $id)
    {
        try {
            // Find the good by ID
            $good = Goods::find($id);
    
            // If the good is not found, return a failure response
            if (!$good) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Good not found.',
                ], 404);
            }
    
            // Delete the good
            $good->delete();
    
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Good deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            // Return error response for any unforeseen issues
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the good.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

