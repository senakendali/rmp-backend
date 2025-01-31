<?php

namespace App\Http\Controllers;

use App\Models\ProcurementLog;
use Illuminate\Http\Request;

class ProcurementLogController extends Controller
{
    // Get all procurement logs
    public function index(Request $request)
    {
        // Start with a base query
        $query = ProcurementLog::with([
            'purchaseRequest',
            'purchaseOrder',
            'purchaseOrderItem',
            'purchaseOrderOffer',
            'purchaseOrderParticipant',
            'user',
        ]);

        // Filter by purchase_request_id if provided
        if ($request->has('purchase_request_id')) {
            $query->where('purchase_request_id', $request->input('purchase_request_id'));
        }

        // Filter by purchase_order_id if provided
        if ($request->has('purchase_order_id')) {
            $query->where('purchase_order_id', $request->input('purchase_order_id'));
        }

        // Execute the query and get the results
        $logs = $query->get();

        return response()->json($logs);
    }

    // Get a single procurement log by ID
    public function show($id)
    {
        $log = ProcurementLog::with([
            'purchaseRequest',
            'purchaseOrder',
            'purchaseOrderItem',
            'purchaseOrderOffer',
            'purchaseOrderParticipant',
            'user',
        ])->find($id);

        if (!$log) {
            return response()->json(['message' => 'Procurement log not found'], 404);
        }

        return response()->json($log);
    }

    // Create a new procurement log
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'purchase_order_offer_id' => 'nullable|exists:purchase_order_offers,id',
            'purchase_order_participant_id' => 'nullable|exists:purchase_order_participants,id',
            'log_name' => 'required|string|max:255',
            'log_description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $log = ProcurementLog::create($validated);

        return response()->json($log, 201);
    }

    // Update a procurement log
    public function update(Request $request, $id)
    {
        $log = ProcurementLog::find($id);

        if (!$log) {
            return response()->json(['message' => 'Procurement log not found'], 404);
        }

        $validated = $request->validate([
            'purchase_request_id' => 'nullable|exists:purchase_requests,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'purchase_order_item_id' => 'nullable|exists:purchase_order_items,id',
            'purchase_order_offer_id' => 'nullable|exists:purchase_order_offers,id',
            'purchase_order_participant_id' => 'nullable|exists:purchase_order_participants,id',
            'log_name' => 'sometimes|required|string|max:255',
            'log_description' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $log->update($validated);

        return response()->json($log);
    }

    // Delete a procurement log
    public function destroy($id)
    {
        $log = ProcurementLog::find($id);

        if (!$log) {
            return response()->json(['message' => 'Procurement log not found'], 404);
        }

        $log->delete();

        return response()->json(['message' => 'Procurement log deleted']);
    }
}