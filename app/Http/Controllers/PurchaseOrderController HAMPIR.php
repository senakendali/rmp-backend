<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Models\GoodsCategories;
use App\Models\Vendors;
use App\Models\Goods;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseOrderParticipant;
use App\Models\User;
use App\Models\PurchaseOrderOffer; 
use App\Models\PurchaseOrderOfferItems;
use App\Models\PurchaseOrderOfferCosts; 
use App\Models\PurchaseOrderPayment;
use App\Models\PurchaseOrderPaymentRecord;
use App\Models\PurchaseOrderAdjustmentNote;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


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
                $query->whereHas('purchaseRequest', function ($q) use ($request) {
                    $q->where('request_type', $request->get('goods_type'));
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
     * Add a vendor to a purchase order
     */
    public function addVendorToPurchaseOrder(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'vendor_id' => 'required|exists:vendors,id',
                'status' => 'required|in:pending,approved,rejected',
                'bod_approval' => 'nullable|in:yes,no',
                'notes' => 'nullable|string',
            ]);
    
            // Add the authenticated user's ID as the creator
            $validatedData['user_created'] = auth()->id();
    
            // Create a new participant record
            $participant = PurchaseOrderParticipant::create($validatedData);
    
            // Return a success response
            return response()->json([
                'success' => true,
                'message' => 'Vendor added to purchase order successfully.',
                'data' => $participant,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    
    /**
     * Confirm vendors on a purchase order  
     */
    public function confirmVendorsOnPurchaseOrder(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'needs_approval'   => 'required|in:yes,no',
            'notes'            => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Find the PurchaseOrder record
            $purchaseOrder = PurchaseOrder::findOrFail($request->purchase_order_id);

            // Update fields
            $purchaseOrder->needs_approval = $request->needs_approval;
            $purchaseOrder->notes = $request->notes;
            $purchaseOrder->user_confirmed = Auth::id(); // Get the ID of the currently authenticated user

            // Save changes
            $purchaseOrder->save();

            return response()->json([
                'status'  => 'success',
                'message' => 'Confirmation is successful.',
                'data'    => $purchaseOrder
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Confirmation is failed.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function manageVendorsForPurchaseOrder(Request $request)
    {
        try {
            // Validate the incoming request data
            $validatedData = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'needs_approval'    => 'required|in:yes,no',
                'notes'             => 'nullable|string',
                'vendors'           => 'required|array',
                'vendors.*.vendor_id' => 'required|exists:vendors,id',
                'vendors.*.status'   => 'required|in:pending,approved,rejected'
            ]);

            // Update the purchase order fields
            $purchaseOrder = PurchaseOrder::findOrFail($validatedData['purchase_order_id']);
            $purchaseOrder->needs_approval = $validatedData['needs_approval'];
            $purchaseOrder->notes = $validatedData['notes'];
            $purchaseOrder->user_confirmed = Auth::id(); // Get the ID of the currently authenticated user
            $purchaseOrder->save();

            // Add vendors to the purchase order
            $vendorsData = $validatedData['vendors'];
            foreach ($vendorsData as $vendor) {
                PurchaseOrderParticipant::create([
                    'purchase_order_id' => $validatedData['purchase_order_id'],
                    'vendor_id'         => $vendor['vendor_id'],
                    'status'            => $vendor['status'],
                    'user_created'      => Auth::id(),
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Vendors managed for purchase order successfully.',
                'data'    => $purchaseOrder,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Handle general errors
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
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
                'participants.vendor',  // Load participants and their vendors
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
            'po_status' => $purchaseOrder->po_status,
            'goods_category_id' => $purchaseOrder->goods_category_id,
            'category_name' => $purchaseOrder->category->name ?? null, // Safely access category name
            'po_name' => $purchaseOrder->po_name,
            'note' => $purchaseOrder->note,
            'items' => $this->transformItems($purchaseOrder->items), // Transform items data
            'vendors' => $this->transformVendors($purchaseOrder->participants), // Transform vendor data
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
     * Transform the vendors associated with the purchase order via participants.
     */
    private function transformVendors($participants)
    {
        return $participants->map(function ($participant) {
            $vendor = $participant->vendor; // The associated Vendor

            return [
                'vendor_id' => $vendor->id, // Vendor ID
                'name' => $vendor->name,
                'pic_name' => $vendor->pic_name,
                'pic_phone' => $vendor->pic_phone,
                'pic_email' => $vendor->pic_email,
                'status' => $participant->status, // Assuming `status` belongs to Participant
                'priority' => null, // Example value, adjust as needed
                'offer_id' => $vendor->purchaseOrderOffers()->where('purchase_order_id', $participant->purchase_order_id)->first()->id ?? null,
                'is_submit_offer' => $vendor->purchaseOrderOffers()->where('purchase_order_id', $participant->purchase_order_id)->exists(),  // Set to true if the vendor has submitted an offer
            ];
        });
    }

     


    /**
     * Record vendor offers for a purchase order.
     */
    public function submitVendorOffers(Request $request) 
    {
        try {
            // Validate the incoming request
            $validated = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'vendor_id' => 'required|exists:vendors,id',
                'payment_method' => 'nullable|in:Bayar Sebagian,Bayar Lunas',
                'delivery_address' => 'nullable|in:Factory,Head Office,Lab Jakarta',
                'delivery_cost' => 'nullable|numeric',
                'offering_document' => 'nullable|file|mimes:pdf,doc,docx',
                'items' => 'required|array',
                'items.*.po_item_id' => 'required|exists:purchase_order_items,id',
                'items.*.offered_price' => 'required|numeric',
                'costs' => 'nullable|array',
                'costs.*.cost_name' => 'required_with:costs|string',
                'costs.*.cost_value' => 'required_with:costs|numeric',
                'payment' => 'required|array',
                'payment.amount' => 'required|numeric',
                'payment.down_payment_amount' => 'required|numeric',
                'payment.records' => 'required|array',
                'payment.records.*.amount_paid' => 'required|numeric',
                'payment.records.*.remarks' => 'nullable|string',
            ]);

            // Handle offering document upload if provided
            $offeringDocumentPath = null;
            if ($request->hasFile('offering_document')) {
                $offeringDocumentPath = $request->file('offering_document')->store('public/offering_documents');
            }

            // Create purchase order offer
            $offer = PurchaseOrderOffer::create([
                'purchase_order_id' => $validated['purchase_order_id'],
                'vendor_id' => $validated['vendor_id'],
                'delivery_address' => $validated['delivery_address'],
                'delivery_cost' => $validated['delivery_cost'],
                'offering_document' => $offeringDocumentPath,
            ]);

            // Create offer items
            foreach ($validated['items'] as $item) {
                PurchaseOrderOfferItems::create([
                    'purchase_order_offer_id' => $offer->id,
                    'po_item_id' => $item['po_item_id'],
                    'offered_price' => $item['offered_price'],
                ]);
            }

            // Create offer costs if any
            if (!empty($validated['costs'])) {
                foreach ($validated['costs'] as $cost) {
                    PurchaseOrderOfferCosts::create([
                        'purchase_order_offer_id' => $offer->id,
                        'cost_name' => $cost['cost_name'],
                        'cost_value' => $cost['cost_value'],
                    ]);
                }
            }

            // Insert into purchase_order_payments
            $payment = $validated['payment'];
            $purchaseOrderPayment = PurchaseOrderPayment::create([
                'purchase_order_offer_id' => $offer->id,
                'payment_method' => $validated['payment_method'] === 'Bayar Lunas' ? 'pay_in_full' : 'pay_in_part',
                'amount' => $payment['amount'],
                'down_payment_amount' => $payment['down_payment_amount'],
            ]);

            // Insert into purchase_order_payment_records
            foreach ($payment['records'] as $record) {
                PurchaseOrderPaymentRecord::create([
                    'purchase_order_payment_id' => $purchaseOrderPayment->id,
                    'amount_paid' => $record['amount_paid'],
                    'remarks' => $record['remarks'] ?? null,
                ]);
            }

            return response()->json([
                'message' => 'Purchase order offer successfully submitted.',
                'offer_id' => $offer->id,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTrace()]);

            return response()->json([
                'message' => 'An error occurred while submitting the purchase order offer.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateVendorOffer(Request $request, $offerId)
    {
        DB::beginTransaction(); // Start the database transaction

        try {
            // Validate the incoming request
            $validated = $request->validate([
                'purchase_order_id' => 'required|exists:purchase_orders,id',
                'vendor_id' => 'required|exists:vendors,id',
                'payment_method' => 'nullable|in:Bayar Sebagian,Bayar Lunas',
                'delivery_address' => 'nullable|in:Factory,Head Office,Lab Jakarta',
                'delivery_cost' => 'nullable|numeric',
                'offering_document' => 'nullable|file|mimes:pdf,doc,docx',
                'items' => 'required|array',
                'items.*.po_item_id' => 'required|exists:purchase_order_items,id',
                'items.*.offered_price' => 'required|numeric',
                'costs' => 'nullable|array',
                'costs.*.cost_name' => 'required_with:costs|string',
                'costs.*.cost_value' => 'required_with:costs|numeric',
                'payment' => 'required|array',
                'payment.amount' => 'required|numeric',
                'payment.down_payment_amount' => 'required|numeric',
                'payment.records' => 'required|array',
                'payment.records.*.amount_paid' => 'required|numeric',
                'payment.records.*.remarks' => 'nullable|string',
            ]);

            // Handle offering document upload if provided
            $offeringDocumentPath = null;
            if ($request->hasFile('offering_document')) {
                $offeringDocumentPath = $request->file('offering_document')->store('public/offering_documents');
            }

            // Find the existing purchase order offer
            $offer = PurchaseOrderOffer::findOrFail($offerId);

            // Update offer details
            $offer->update([
                'purchase_order_id' => $validated['purchase_order_id'],
                'vendor_id' => $validated['vendor_id'],
                'delivery_address' => $validated['delivery_address'],
                'delivery_cost' => $validated['delivery_cost'],
                'offering_document' => $offeringDocumentPath ?? $offer->offering_document,
            ]);

            // Update or create offer items
            foreach ($validated['items'] as $item) {
                PurchaseOrderOfferItems::updateOrCreate(
                    [
                        'purchase_order_offer_id' => $offer->id,
                        'po_item_id' => $item['po_item_id'], // Using po_item_id as unique identifier
                    ],
                    [
                        'offered_price' => $item['offered_price'],
                    ]
                );
            }

            // Update or create offer costs
            if (!empty($validated['costs'])) {
                foreach ($validated['costs'] as $cost) {
                    PurchaseOrderOfferCosts::updateOrCreate(
                        [
                            'purchase_order_offer_id' => $offer->id,
                            'cost_name' => $cost['cost_name'], // Using cost_name as unique identifier (or can be any unique identifier)
                        ],
                        [
                            'cost_value' => $cost['cost_value'],
                        ]
                    );
                }
            }

            // Update or create payment details
            $payment = $validated['payment'];
            $purchaseOrderPayment = $offer->purchaseOrderPayments()->first();

            
            if ($purchaseOrderPayment) {
                $purchaseOrderPayment->update([
                    'payment_method' => $validated['payment_method'] === 'Bayar Lunas' ? 'pay_in_full' : 'pay_in_part',
                    'amount' => $payment['amount'],
                    'down_payment_amount' => $payment['down_payment_amount'],
                ]);
            } else {
                $purchaseOrderPayment = PurchaseOrderPayment::create([
                    'purchase_order_offer_id' => $offer->id,
                    'payment_method' => $validated['payment_method'] === 'Bayar Lunas' ? 'pay_in_full' : 'pay_in_part',
                    'amount' => $payment['amount'],
                    'down_payment_amount' => $payment['down_payment_amount'],
                ]);
            }

            // Update or create payment records
            foreach ($payment['records'] as $record) {
                PurchaseOrderPaymentRecord::updateOrCreate(
                    [
                        'purchase_order_payment_id' => $purchaseOrderPayment->id,
                        'amount_paid' => $record['amount_paid'], // Use the unique identifier (amount_paid)
                    ],
                    [
                        'remarks' => $record['remarks'] ?? null,
                    ]
                );
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'message' => 'Purchase order offer successfully updated.',
                'offer_id' => $offer->id,
            ], 200);

        } catch (ValidationException $e) {
            DB::rollBack(); // Rollback the transaction on validation failure
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            DB::rollBack(); // Rollback the transaction on any other exception
            Log::error($e->getMessage(), ['trace' => $e->getTrace()]);

            return response()->json([
                'message' => 'An error occurred while updating the purchase order offer.',
                'error' => $e->getMessage(),
            ], 500);
        }
}




    /**
     * Fetch vendor offer details
     * 
     * @param int $offerId
     */
    public function fetchVendorOfferDetails($offerId)
    {
        try {
            // Retrieve the PurchaseOrderOffer by ID with all related data
            $offer = PurchaseOrderOffer::with([
                'purchaseOrderOfferItems.purchaseOrderItem.purchaseRequestItem.goods', // Use correct relationships
                'purchaseOrderCosts',
                'purchaseOrderPayments.paymentRecords',
            ])->findOrFail($offerId);

            // Prepare the response data
            $response = [
                'offer_id' => $offer->id,
                'purchase_order_id' => $offer->purchase_order_id,
                'vendor_id' => $offer->vendor_id,
                'vendor_detail' => array(
                    'vendor_name' => $offer->vendor->name,
                    'vendor_address' => $offer->vendor->address,
                    'vendor_pic_name' => $offer->vendor->pic_name,
                    'vendor_pic_phone' => $offer->vendor->pic_phone,
                    'vendor_pic_email' => $offer->vendor->pic_email,
                ),
                'delivery_address' => $offer->delivery_address,
                'delivery_cost' => $offer->delivery_cost,
                'offering_document' => $offer->offering_document,
                'items' => $offer->purchaseOrderOfferItems->map(function ($item) {
                    $purchaseOrderItem = $item->purchaseOrderItem; // Correctly load related PurchaseOrderItem
                    $goods = $purchaseOrderItem?->goods; // Safely access Goods data

                    return [
                        'purchase_request_id' => $purchaseOrderItem?->purchaseRequestItem->id ?? null,
                        'po_item_id' => $purchaseOrderItem?->id ?? null, // Use correct column
                        'goods_id' => $goods?->id ?? null, // Goods data may be null
                        'goods_name' => $goods?->name ?? null,
                        'quantity' => $purchaseOrderItem?->quantity,
                        'measurement_id' => $purchaseOrderItem?->measurement_id ?? null,
                        'measurement' => $purchaseOrderItem?->measurementUnit->name ?? null,
                        'offered_price' => $item->offered_price,
                    ];
                }),
                'costs' => $offer->purchaseOrderCosts->map(function ($cost) {
                    return [
                        'cost_name' => $cost->cost_name,
                        'cost_value' => $cost->cost_value,
                    ];
                }),
                'payments' => $offer->purchaseOrderPayments->map(function ($payment) {
                    return [
                        'payment_method' => $payment->payment_method,
                        'amount' => $payment->amount,
                        'down_payment_amount' => $payment->down_payment_amount,
                        'payment_records' => $payment->paymentRecords->map(function ($record) {
                            return [
                                'payment_id' => $record->id,
                                'amount_paid' => $record->amount_paid,
                                'remarks' => $record->remarks,
                            ];
                        }),
                    ];
                }),
            ];

            return response()->json([
                'message' => 'Vendor offer details fetched successfully.',
                'offer_details' => $response,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Vendor offer not found.',
            ], 404);
        } catch (Exception $e) {
            Log::error($e->getMessage(), ['trace' => $e->getTrace()]);

            return response()->json([
                'message' => 'An error occurred while fetching the vendor offer details.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    //ENUM('Belum Diproses', 'Menunggu Persetujuan', 'Disetujui', 'Ditolak', 'Direvisi', 'PO Rilis', 'Pengiriman', 'PO Selesai')
    public function purchaseOrderVerification(Request $request)
    {
       
           // Validate the request
        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_orders,id',
            'po_status' => 'required|in:Disetujui,Ditolak,Direvisi',
            'note' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update the PurchaseOrder status
            $purchaseOrder = PurchaseOrder::findOrFail($request->purchase_order_id);
            $purchaseOrder->po_status = $request->po_status;

            if($request->po_status == 'Disetujui'){
                $purchaseOrder->user_confirmed = Auth::id();
                $purchaseOrder->confirmed_at = now();
            }

            $purchaseOrder->save();

            // Insert a record into PurchaseOrderAdjustmentNotes
            PurchaseOrderAdjustmentNote::create([
                'purchase_order_id' => $request->purchase_order_id,
                'user_id' => Auth::id(),
                'note' => $request->note,
            ]);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Purchase order verification successful.',
                'data' => $purchaseOrder,
            ], 200);

        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred during purchase order verification.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function replyAdjustmentNote(Request $request, $id)
    {
           // Validate the request
        $validator = Validator::make($request->all(), [
            'note' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Insert a record into PurchaseOrderAdjustmentNotes
            PurchaseOrderAdjustmentNote::create([
                'purchase_order_id' => $id,
                'user_id' => Auth::id(),
                'note' => $request->note,
            ]);

            // Commit the transaction
            DB::commit();

            return response()->json([
                'message' => 'Purchase order verification successful.',
                'data' => PurchaseOrderAdjustmentNote::where('purchase_order_id', $id)->get(),
            ], 200);

        } catch (\Exception $e) {
            // Rollback the transaction on error
            DB::rollBack();

            return response()->json([
                'message' => 'An error occurred during purchase order verification.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getAdjustmentNotes($id)
    {
        return response()->json([
            'data' => PurchaseOrderAdjustmentNote::where('purchase_order_id', $id)->get(),
        ], 200);
    }

    public function releasePurchaseOrder(Request $request, $id)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpeg,jpg,png|max:2048',
        ]);

        $purchaseOrder = PurchaseOrder::findOrFail($id);
        
        // Pastikan folder penyimpanan ada
        $directory = 'purchase_order';
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Simpan file ke storage/purchase_order
        $path = $request->file('document')->store($directory, 'public');
        
        // Update status PO dan simpan path dokumen
        $purchaseOrder->update([
            'po_status' => 'PO Rilis',
            'signed_po_document' => $path,
            'po_release_by' => Auth::id(),
            'po_release_date' => now()
        ]);

        return response()->json([
            'message' => 'Purchase Order released successfully',
            'data' => $purchaseOrder
        ]);
    }

    public function confirmPayment(Request $request, $id)
    {
        $request->validate([
            'payment_struk' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'delivery_order_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'sales_order_document' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $directory = 'purchase_order/payment';

        // Ensure directory exists
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory, 0775, true);
        }

        DB::beginTransaction(); // Start transaction

        try {
            $paymentRecord = PurchaseOrderPaymentRecord::findOrFail($id);

            // Check if the files exist and delete old ones before saving new ones
            if ($request->hasFile('payment_struk')) {
                if ($paymentRecord->payment_struk) {
                    Storage::delete($paymentRecord->payment_struk);
                }
                $paymentRecord->payment_struk = $request->file('payment_struk')->store($directory);
            }

            if ($request->hasFile('delivery_order_document')) {
                if ($paymentRecord->delivery_order_document) {
                    Storage::delete($paymentRecord->delivery_order_document);
                }
                $paymentRecord->delivery_order_document = $request->file('delivery_order_document')->store($directory);
            }

            if ($request->hasFile('sales_order_document')) {
                if ($paymentRecord->sales_order_document) {
                    Storage::delete($paymentRecord->sales_order_document);
                }
                $paymentRecord->sales_order_document = $request->file('sales_order_document')->store($directory);
            }

            $paymentRecord->save();
            DB::commit(); // Commit transaction

            return response()->json([
                'message' => 'Payment record updated successfully!',
                'data' => $paymentRecord
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback changes if error occurs
            Log::error('Confirm Payment Error: ' . $e->getMessage()); // Log error for debugging

            return response()->json([
                'message' => 'An error occurred while updating the payment record.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getPaymentList(Request $request)
    {
        // Define the number of records per page
        $perPage = $request->input('per_page', 10);

        // Retrieve paginated list of payments with related data
        $payments = PurchaseOrderPayment::with([
            'purchaseOrderOffer.purchaseOrder',
            'purchaseOrderOffer.vendor',
            'paymentRecords',
        ])
        ->paginate($perPage);

        // Transform the paginated data
        $transformedPayments = $payments->through(function ($payment) {
            return [
                'payment_id' => $payment->id,
                'po_id' => $payment->purchaseOrderOffer->purchaseOrder->id ?? null,
                'po_name' => $payment->purchaseOrderOffer->purchaseOrder->po_name ?? null,
                'po_type' => $payment->purchaseOrderOffer->purchaseOrder->po_type ?? null,
                'vendor_name' => $payment->purchaseOrderOffer->vendor->name ?? null,
                'amount' => $payment->amount,
                'category' => $payment->purchaseOrderOffer->purchaseOrder->category->name ?? null,
                'payment_method' => ($payment->payment_method == 'pay_in_full') ? 'Bayar Lunas' : 'Bayar Sebagian',
                'payment_date' => $payment->created_at->format('d F Y'),
                'status' => $payment->status ?? 'Belum Lunas',
            ];
        });

        // Return the paginated and transformed data as a JSON response
        return response()->json($transformedPayments);
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



  