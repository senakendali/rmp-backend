<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the purchase requests.
     */
    public function index()
    {
        return response()->json(PurchaseRequest::with('items')->paginate(10));
    }

    /**
     * Store a newly created purchase request in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'buyer' => 'nullable|string|max:255',
                'purchase_reason' => 'nullable|string|max:255',
                'purchase_reason_detail' => 'nullable|string|max:255',
                'department_id' => 'nullable|exists:departments,id',
                'notes' => 'nullable|string',
                'created_by' => 'required|string|max:255',
                'items' => 'required|array',
                'items.*.goods_id' => 'required|exists:goods,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.measurement' => 'required|string|max:255',
            ]);

            // Calculate total items
            $totalItems = count($validated['items']);
            $currentDate = now(); // Use the current date and time

            // Create purchase request
            $purchaseRequest = PurchaseRequest::create([
                'request_date' => $currentDate,
                'buyer' => $validated['buyer'] ?? null,
                'purchase_reason' => $validated['purchase_reason'] ?? null,
                'purchase_reason_detail' => $validated['purchase_reason_detail'] ?? null,
                'department_id' => 1, // Default department ID
                'total_items' => $totalItems,
                'notes' => $validated['notes'] ?? null,
                'created_by' => $validated['created_by'],
            ]);

            // Add items to the purchase request
            foreach ($validated['items'] as $item) {
                $purchaseRequest->items()->create($item);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase request created successfully.',
                'data' => $purchaseRequest->load('items'),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while creating the purchase request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified purchase request.
     */
    public function show($id)
    {
        $purchaseRequest = PurchaseRequest::with('items')->find($id);

        if (!$purchaseRequest) {
            return response()->json(['message' => 'Purchase request not found.'], 404);
        }

        return response()->json($purchaseRequest);
    }

    /**
     * Update the specified purchase request in storage.
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = PurchaseRequest::findOrFail($id);

            $validated = $request->validate([
                'buyer' => 'nullable|string|max:255',
                'purchase_reason' => 'nullable|string|max:255',
                'purchase_reason_detail' => 'nullable|string|max:255',
                'notes' => 'nullable|string',
                'updated_by' => 'required|string|max:255',
                'items' => 'nullable|array', // Items can be optional during update
                'items.*.goods_id' => 'required_with:items|exists:goods,id',
                'items.*.quantity' => 'required_with:items|integer|min:1',
                'items.*.measurement' => 'required_with:items|string|max:255',
            ]);

            // Update fields in purchase request
            $purchaseRequest->update([
                'buyer' => $validated['buyer'] ?? $purchaseRequest->buyer,
                'purchase_reason' => $validated['purchase_reason'] ?? $purchaseRequest->purchase_reason,
                'purchase_reason_detail' => $validated['purchase_reason_detail'] ?? $purchaseRequest->purchase_reason_detail,
                'notes' => $validated['notes'] ?? $purchaseRequest->notes,
                'updated_by' => 'System', // Mandatory field
            ]);

            if (isset($validated['items'])) {
                // Remove existing items and add the updated items
                $purchaseRequest->items()->delete();

                foreach ($validated['items'] as $item) {
                    $purchaseRequest->items()->create($item);
                }

                // Update total_items based on new items
                $purchaseRequest->update([
                    'total_items' => count($validated['items']),
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase request updated successfully.',
                'data' => $purchaseRequest->load('items'),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the purchase request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function followUp(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = PurchaseRequest::findOrFail($id);

            if ($purchaseRequest->buyer !== null) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This purchase request already has a buyer.',
                ], 400);
            }

            $validated = $request->validate([
                'buyer' => 'required|string|max:255'
            ]);

            $purchaseRequest->update([
                'buyer' => $validated['buyer'],
                'followed_by' => 'System',
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase request followed up successfully.',
                'data' => $purchaseRequest,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during the follow-up process.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $purchaseRequest = PurchaseRequest::findOrFail($id);

            $validated = $request->validate([
                'status' => 'required|in:approved,revised,rejected',
                'update_status_reason' => 'nullable|string|max:1000', // Optional by default
                'update_status_by' => 'nullable|string|max:255', 
            ]);

            // Ensure reason is provided for revised or rejected statuses
            if (in_array($validated['status'], ['revised', 'rejected']) && empty($validated['update_status_reason'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Reason is required when the status is "revised" or "rejected".',
                ], 422);
            }

            $updateData = [
                'status' => $validated['status'],
                'update_status_by' => "System",
            ];

            if (!empty($validated['update_status_reason'])) {
                $updateData['update_status_reason'] = $validated['update_status_reason'];
            }

            // Set approval date if the status is approved
            if ($validated['status'] === 'approved') {
                $updateData['approval_date'] = now();
            }

            $purchaseRequest->update($updateData);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase request status updated successfully.',
                'data' => $purchaseRequest,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the purchase request status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPurchaseHistory($goodsId, $departmentId){
        try {
            $purchaseHistory = PurchaseRequestItem::with(['purchaseRequest', 'goods'])
                ->where('goods_id', $goodsId)
                ->whereHas('purchaseRequest', function ($query) use ($departmentId) {
                    $query->where('department_id', $departmentId);
                })
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'purchase_request_id' => $item->purchase_request_id,
                        'goods_id' => $item->goods_id,
                        'goods_name' => $item->goods ? $item->goods->name : null, // Extract name from goods
                        'quantity' => $item->quantity,
                        'measurement' => $item->measurement,
                        'purchase_request' => $item->purchaseRequest, // Include full purchase request details
                        'created_at' => $item->created_at,
                        'updated_at' => $item->updated_at,
                    ];
                });
        
            return response()->json([
                'success' => true,
                'data' => $purchaseHistory
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching purchase history.',
                'error' => $e->getMessage()
            ], 500);
        }
            
    }

    /**
     * Remove the specified purchase request from storage.
     */
    public function destroy($id)
    {
        try {
            $purchaseRequest = PurchaseRequest::find($id);

            if (!$purchaseRequest) {
                return response()->json(['message' => 'Purchase request not found.'], 404);
            }

            $purchaseRequest->delete();

            return response()->json(['message' => 'Purchase request deleted successfully.'], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the purchase request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
