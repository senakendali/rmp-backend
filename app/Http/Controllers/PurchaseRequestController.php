<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
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
                'request_date' => 'required|date',
                'buyer' => 'required|string|max:255',
                'total_items' => 'required|integer',
                'notes' => 'nullable|string',
                'created_by' => 'required|string|max:255',
                'items' => 'required|array',
                'items.*.goods_id' => 'required|exists:goods,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.measurement' => 'required|string|max:255',
            ]);

            // Create purchase request
            $purchaseRequest = PurchaseRequest::create([
                'request_date' => $validated['request_date'],
                'buyer' => $validated['buyer'],
                'total_items' => $validated['total_items'],
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
            $purchaseRequest = PurchaseRequest::find($id);

            if (!$purchaseRequest) {
                return response()->json(['message' => 'Purchase request not found.'], 404);
            }

            $validated = $request->validate([
                'request_date' => 'sometimes|date',
                'buyer' => 'sometimes|string|max:255',
                'total_items' => 'sometimes|integer',
                'notes' => 'nullable|string',
                'updated_by' => 'required|string|max:255',
            ]);

            // Update purchase request
            $purchaseRequest->update($validated);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Purchase request updated successfully.',
                'data' => $purchaseRequest,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the purchase request.',
                'error' => $e->getMessage(),
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
