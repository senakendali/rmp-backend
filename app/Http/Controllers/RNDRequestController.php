<?php

namespace App\Http\Controllers;

use App\Models\RndRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RNDRequestController extends Controller
{
    public function index()
    {
        $requests = RndRequest::paginate(10);
        return response()->json($requests);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string',
                'development_type' => 'required|in:Produk Baru,Produk Lama',
                'launching_date' => 'required|date',
                'description' => 'required|string',
                'category' => 'required|in:Obat Bahan Alam,Suplemen Kesehatan,Kosmetik',
                'priority' => 'required|in:Rendah,Sedang,Tinggi',
            ]);

            $validated['requested_by'] = Auth::id();
            $RndRequest = RndRequest::create($validated);
            return response()->json($RndRequest, Response::HTTP_CREATED);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to store data', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $RndRequest = RndRequest::findOrFail($id);
            return response()->json($RndRequest);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to fetch data', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $RndRequest = RndRequest::findOrFail($id);

            if ($request->isMethod('patch')) {
                $validated = $request->validate([
                    'title' => 'sometimes|string',
                    'development_type' => 'sometimes|in:Produk Baru,Produk Lama',
                    'launching_date' => 'sometimes|date',
                    'description' => 'sometimes|string',
                    'category' => 'sometimes|in:Obat Bahan Alam,Suplemen Kesehatan,Kosmetik',
                    'priority' => 'sometimes|in:Rendah,Sedang,Tinggi',
                ]);
            } else {
                $validated = $request->validate([
                    'title' => 'required|string',
                    'development_type' => 'required|in:Produk Baru,Produk Lama',
                    'launching_date' => 'required|date',
                    'description' => 'required|string',
                    'category' => 'required|in:Obat Bahan Alam,Suplemen Kesehatan,Kosmetik',
                    'priority' => 'required|in:Rendah,Sedang,Tinggi',
                ]);
            }

            $RndRequest->update($validated);
            return response()->json($RndRequest);
        } catch (\Illuminate\Validation\ValidationThrowable $e) {
            return response()->json(['error' => 'Validation failed', 'message' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to update data', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $RndRequest = RndRequest::findOrFail($id);
          
            $RndRequest->delete();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to delete data', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
