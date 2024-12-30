<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\GoodsCategories;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // Query to get category name and total number of items per category
            $categories = GoodsCategories::withCount(['goods as total_items' => function ($query) {
                $query->join('purchase_request_items', 'purchase_request_items.goods_id', '=', 'goods.id');
            }])
            ->get()
            ->map(function ($category) {
                return [
                    'category_name' => $category->name,
                    'total_items' => $category->total_items,
                ];
            });
    
           
    
            return response()->json($categories);
        } catch (\Exception $e) {
            
    
            // Return error response with error details (if in debug mode)
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the category item counts.',
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,  // Show stack trace only in debug mode
            ], 500);
        }
    }

   
    //Get category name and total number of items per category
    public function getCategoryItemCount()
    {
        

        try {
            // Query to get category name and total number of items per category
            $categories = GoodsCategories::withCount(['goods as total_items' => function ($query) {
                $query->join('purchase_request_items', 'purchase_request_items.goods_id', '=', 'goods.id');
            }])
            ->get()
            ->map(function ($category) {
                return [
                    'category_name' => $category->name,
                    'total_items' => $category->total_items,
                ];
            });
    
           
    
            return response()->json($categories);
        } catch (\Exception $e) {
            
    
            // Return error response with error details (if in debug mode)
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the category item counts.',
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,  // Show stack trace only in debug mode
            ], 500);
        }
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
