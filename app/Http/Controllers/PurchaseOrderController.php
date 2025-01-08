<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\GoodsCategories;
use App\Models\Goods;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function index(Request $request)
     {
         // Start the query to fetch all PurchaseOrders
         $query = PurchaseOrder::query();
     
         // Filter by po_type if provided
         switch ($request->get('po_type')) {
             case 'material':
                 $query->whereHas('items', function ($q) {
                     $q->where('po_type', 'material');
                 });
                 break;
             case 'non-material':
                 $query->whereHas('items', function ($q) {
                     $q->where('po_type', 'non-material');
                 });
                 break;
             default:
                 // Default mode (no additional filter applied)
                 break;
         }
     
         // Filter by category if 'category_id' is provided
         if ($categoryId = $request->get('category_id')) {
             $query->where('category_id', $categoryId);  // Assuming 'category_id' is the column in the PurchaseOrder table
         }
     
         // Eager load the 'category' relationship to get category details (e.g., name)
         $query->with('category');
     
         // Paginate the results and return as JSON response
         return response()->json($query->paginate(10));
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
            // Initialize the query for PurchaseRequestItem
            $query = PurchaseRequestItem::select('id', 'goods_id', 'quantity', 'measurement_id', 'purchase_request_id') 
                ->with([
                    'purchaseRequest:id,approval_date,status,department_id', // Include department_id
                    'purchaseRequest.department:id,name', // Fetch id and name from the department
                    'goods:id,name,goods_category_id',
                    'goods.category:id,name',
                    'measurementUnit:id,name'
                ])
                ->whereHas('purchaseRequest', function ($q) {
                    $q->where('status', 'approved'); // Filter for approved status
                });

            // Optionally apply filters
            if ($request->has('goods_type')) {
                $query->whereHas('goods', function ($q) use ($request) {
                    $q->where('goods_type', $request->get('goods_type'));
                });
            }

            if($request->has('category_id')) {
                $query->whereHas('goods', function ($q) use ($request) {
                    $q->where('goods_category_id', $request->get('category_id'));
                });
            }

            

            // Get the items
            $items = $query->get();

            // Transform the data
            $transformedItems = $items->map(function ($item) {
                return [
                    'purchase_request_item_id' => $item->id,
                    'purchase_request_id' => $item->purchase_request_id,
                    'approval_date' => $item->purchaseRequest->approval_date ?? null,
                    'goods_id' => $item->goods_id,
                    'goods_name' => $item->goods->name ?? null,
                    'goods_category_id' => $item->goods->category->id ?? null,
                    'goods_category_name' => $item->goods->category->name ?? null,
                    'department_id' => $item->purchaseRequest->department->id ?? null, // Retrieve department id
                    'department_name' => $item->purchaseRequest->department->name ?? null, // Retrieve department name   
                    'quantity' => $item->quantity,
                    'measurement_id' => $item->measurementUnit->id,
                    'measurement' => $item->measurementUnit->name ?? null,
                ];
            });

            // Return the response
            return response()->json([
                'status' => 'success',
                'data' => $transformedItems,
            ]);
        } catch (\Throwable $e) {
            // Handle errors
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

    public function addItemToPo(Request $request)
    {
        try {
            // Validate the input fields
            $validatedData = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'request_item_id'   => 'required|exists:purchase_request_items,id',
            ]);
    
            // Fetch the PurchaseRequestItem details
            $requestItemDetails = PurchaseRequestItem::where('id', $validatedData['request_item_id'])
                ->with([
                    'purchaseRequest:id,approval_date,status,department_id', // Include department_id
                    'purchaseRequest.department:id,name',                   // Fetch id and name from the department
                    'goods:id,name,goods_category_id',
                    'goods.category:id,name',
                    'measurementUnit:id,name'
                ])
                ->first();
    
            // Ensure the request item exists and is valid
            if (!$requestItemDetails) {
                return response()->json([
                    'success' => false,
                    'message' => 'Request item not found.',
                ], 404);
            }
    
            // Construct the item data for the purchase order
            $items = [
                'purchase_order_id' => $validatedData['purchase_order_id'],
                'purchase_request_item_id' => $requestItemDetails->id,
                'goods_id' => $requestItemDetails->goods_id,
                'department_id' => $requestItemDetails->purchaseRequest->department_id ?? null,
                'quantity' => $requestItemDetails->quantity,
                'measurement_id' => $requestItemDetails->measurement_id,
                'user_created' => Auth::id(), // Get the ID of the currently authenticated user
            ];
    
            // Create the purchase order item
            $purchaseOrderItem = PurchaseOrderItem::create($items);
    
            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Item added to purchase order successfully.',
                'data' => $purchaseOrderItem,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            // Handle unexpected errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : 'Please contact support.', // Hide detailed error in production
            ], 500);
        }
    }

    public function moveItemToAnotherPo(Request $request)
    {
        try {
            // Validate the input fields
            $validatedData = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'request_item_id'   => 'required|exists:purchase_request_items,id',
            ]);

            // Find the existing PurchaseOrderItem by request_item_id
            $existingItem = PurchaseOrderItem::where('purchase_request_item_id', $validatedData['request_item_id'])->first();

            if (!$existingItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'The item does not exist in any purchase order.',
                ], 404);
            }

            // Update the purchase_order_id to move the item
            $existingItem->update([
                'purchase_order_id' => $validatedData['purchase_order_id'],
                'user_updated' => Auth::id(), // Track the user who updated the record
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item moved to the new purchase order successfully.',
                'data' => $existingItem,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            // Handle unexpected errors
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
                'error' => config('app.debug') ? $e->getMessage() : 'Please contact support.', // Hide detailed error in production
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
     
     public function show($id)
     {
         try {
             // Load the purchase order with related items, goods, categories, and measurement units
             $purchaseOrder = PurchaseOrder::with([
                 'items.goods.category',  // Load goods and their category
                 'category',              // Load the category of the purchase order
                 'items.measurementUnit',  // Load the measurement unit for items
                 'items.PurchaseRequestItem',  // Load the purchase request for items
                 'items.department',  // Load the department for items
             ])->findOrFail($id);  // Automatically returns 404 if not found
     
             // Transform the response to include only the necessary details
             $purchaseOrderData = $this->transformPurchaseOrder($purchaseOrder);
     
             return response()->json($purchaseOrderData);
         } catch (ModelNotFoundException $e) {
             // Catch if the PurchaseOrder is not found and return a 404 error
             \Log::error('PurchaseOrder not found: ' . $e->getMessage());
             return response()->json(['message' => 'Purchase order not found.'], 404);
         } catch (\Exception $e) {
             // Catch any other errors and log them for debugging
             \Log::error('Error fetching purchase order data: ' . $e->getMessage());
             
             // Optionally, you can log the entire exception to get more context
             \Log::error('Exception details: ' . $e);
     
             return response()->json([
                 'message' => 'An error occurred while processing your request.',
                 'error' => $e->getMessage(),
             ], 500);
         }
     }
     
     /**
      * Transform the PurchaseOrder data into a simplified format.
      */
     private function transformPurchaseOrder($purchaseOrder)
     {
         return [
             'id' => $purchaseOrder->id,
             'po_date' => $purchaseOrder->created_at,
             'po_number' => $purchaseOrder->purchase_order_number,
             'po_type' => $purchaseOrder->po_type,
             'category_id' => $purchaseOrder->category_id,
             'category_name' => $purchaseOrder->category->name ?? null, // Safely access category name
             'po_name' => $purchaseOrder->po_name,
             'note' => $purchaseOrder->note,
             'items' => $this->transformItems($purchaseOrder->items), // Transform items data
         ];
     }
     
     /**
      * Transform each item in the purchase order into the desired format.
      */
     private function transformItems($items)
     {
         return $items->map(function ($item) {
             return [
                 'id' => $item->id,
                 'purchase_request_id' => $item->PurchaseRequestItem->purchase_request_id,
                 'goods_id' => $item->goods_id,
                 'goods_name' => $item->goods->name ?? null,  // Safely access goods name
                 'goods_category_name' => $item->goods->category->name ?? null, // Safely access category name
                 'department_name' => $item->department->name ?? null, // Safely access department name
                 'quantity' => $item->quantity ?? null,  // Quantity
                 'measurement_id' => $item->measurementUnit->id ?? null,  // Safely access measurement unit ID
                 'measurement' => $item->measurementUnit->name ?? null,  // Safely access measurement unit name
             ];
         });
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



  