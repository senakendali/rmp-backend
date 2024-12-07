<?php

namespace App\Http\Controllers;

use App\Models\Goods;
use App\Models\GoodsCategories;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class GoodsManagementController extends Controller
{
    // Get all goods
    public function index()
    {
        try {
            $goods = Goods::with('category')->get(); // Eager load the category relationship
            return response()->json($goods);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to retrieve goods.',
                'error' => $e->getMessage()
            ], 500); // Internal server error
        }
    }

    // Get a single good
    public function show($id)
    {
        try {
            $good = Goods::with('category')->findOrFail($id); // Automatically throws ModelNotFoundException if not found
            return response()->json($good);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Good not found',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred.',
                'error' => $e->getMessage()
            ], 500); // Internal server error
        }
    }

    // Create a new good
    public function store(Request $request)
    {
        try {
            $request->validate([
                'goods_category_id' => 'required|exists:goods_categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'measurement' => 'required|string',
            ]);

            $good = Goods::create($request->all());
            return response()->json($good, 201); // Created
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to create good.',
                'error' => $e->getMessage()
            ], 500); // Internal server error
        }
    }

    // Update an existing good
    public function update(Request $request, $id)
    {
        try {
            $good = Goods::findOrFail($id); // Automatically throws ModelNotFoundException if not found
            
            $request->validate([
                'goods_category_id' => 'required|exists:goods_categories,id',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'measurement' => 'required|string',
            ]);

            $good->update($request->all());
            return response()->json($good);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Good not found.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to update good.',
                'error' => $e->getMessage()
            ], 500); // Internal server error
        }
    }

    // Delete a good
    public function destroy($id)
    {
        try {
            $good = Goods::findOrFail($id); // Automatically throws ModelNotFoundException if not found
            $good->delete();

            return response()->json([
                'message' => 'Good deleted successfully.'
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Good not found.',
                'error' => $e->getMessage()
            ], 404); // Not Found
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Failed to delete good.',
                'error' => $e->getMessage()
            ], 500); // Internal server error
        }
    }
}
