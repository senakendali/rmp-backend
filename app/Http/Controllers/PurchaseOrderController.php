<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\GoodsCategories;
use App\Models\Goods;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
    }

   
    //Get category name and total number of items per category
    public function getCategoryItemCount(Request $request)
    {
        try {
            // Initialize the query
            $query = GoodsCategories::withCount(['goods as total_items' => function ($query) {
                $query->join('purchase_request_items', 'purchase_request_items.goods_id', '=', 'goods.id')
                    ->join('purchase_requests', 'purchase_request_items.purchase_request_id', '=', 'purchase_requests.id')
                    ->where('purchase_requests.status', 'approved'); // Filter by purchaseRequest status
            }]);

            // Apply filter by goods_type if provided in the request
            if ($request->has('goods_type')) {
                $query->whereHas('goods', function ($query) use ($request) {
                    $query->where('goods_type', $request->get('goods_type'));
                });
            }

            // Fetch categories and count total items
            $categories = $query->get()->map(function ($category) {
                return [
                    'category_name' => $category->name,
                    'total_items' => $category->total_items,
                ];
            });

            // Return the result
            return response()->json([
                'status' => 'success',
                'data' => $categories,
            ]);

        } catch (\Exception $e) {
            // Return error response with error details
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching the category item counts.',
                'error' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }


    /**
     * Retrieve all items with their related category, goods, and measurement unit.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ItemQueues(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            // Initialize the query for PurchaseRequestItem and select specific fields
            $query = PurchaseRequestItem::select('id', 'goods_id', 'quantity', 'measurement_id', 'purchase_request_id') // Fetch only necessary columns
                ->with([
                    'purchaseRequest:id,approval_date,status', // Fetch specific fields from 'purchaseRequest'
                    'goods:id,name,goods_category_id',               // Fetch specific fields from 'goods'
                    'goods.category:id,name',                  // Fetch specific fields from 'category'
                    'measurementUnit:id,name'                  // Fetch specific fields from 'measurementUnit'
                ])
                ->whereHas('purchaseRequest', function ($q) {
                    $q->where('status', 'approved'); // Filter by 'status' on 'purchaseRequest' right from the start
                });

            // Optionally, apply other filters (e.g., by goods_type)
            if ($request->has('goods_type')) {
                $query->whereHas('goods', function ($q) use ($request) {
                    $q->where('goods_type', $request->get('goods_type')); // Filter by 'goods_type' in 'goods'
                });
            }

            // Fetch the filtered data with the selected fields
            $items = $query->get();

            // Transform the data with null checks
            $transformedItems = $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'approval_date' => $item->purchaseRequest->approval_date ?? null, // Null-safe access
                    'goods_name' => $item->goods->name ?? null,                       // Null-safe access
                    'goods_category_name' => $item->goods->category->name ?? null,    // Null-safe access
                    'purchase_request_id' => $item->purchase_request_id,
                    'goods_id' => $item->goods_id,
                    'quantity' => $item->quantity,
                    'measurement_unit_id' => $item->measurement_unit_id,
                    'memasurement' => $item->measurementUnit->name ?? null,          // Null-safe access
                ];
            });

            // Return the data in JSON format
            return response()->json([
                'status' => 'success',
                'data' => $transformedItems,
            ]);
        } catch (\Throwable $e) {
            // Handle exceptions and return an error response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while fetching goods.',
                'error' => $e->getMessage(),
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



  