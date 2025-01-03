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
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;

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
                    'goods_category_id' => $item->goods->category->id ?? null,          // Null-safe access
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

    

    public function createPo(Request $request)
    {
        try {
            // Validate the input fields
            $validatedData = $request->validate([
                'goods_category_id' => 'required|integer|exists:goods_category,id', // assuming goods_categories exists
                'po_type'           => 'required|string|max:255',
                'po_name'           => 'required|string|max:255',
                'note'              => 'nullable|string',
            ]);

            // Fetch the maximum purchase order number
            $latestNumber = PurchaseOrder::max('purchase_order_number');

            if ($latestNumber) {
                // Extract the numeric part after the prefix 'PO'
                $numericValue = intval(substr($latestNumber, 2));
            } else {
                $numericValue = 0; // No records exist yet
            }

            // Increment and format the next number
            $nextNumber = str_pad($numericValue + 1, 6, '0', STR_PAD_LEFT);
            $purchaseOrderNumber = 'PO' . $nextNumber; // Final PO number

            // Add the generated purchase order number and the authenticated user ID
            $validatedData['purchase_order_number'] = $purchaseOrderNumber;
            $validatedData['user_created'] = Auth::id(); // Get the ID of the currently authenticated user

            // Create the purchase order
            $purchaseOrder = PurchaseOrder::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Purchase order created successfully.',
                'data'    => $purchaseOrder,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors'  => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error'   => $e->getMessage(), // You might want to hide this in production
            ], 500);
        }
    }

    public function listPo()
    {
        try {
            // Retrieve only 'id' and 'purchase_order_number' fields
            $purchaseOrders = PurchaseOrder::select('id', 'purchase_order_number', 'po_name')->get();

            return response()->json([
                'success' => true,
                'data'    => $purchaseOrders,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error'   => $e->getMessage(),
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



  