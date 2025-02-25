<?php

namespace App\Http\Controllers;

use App\Models\RndProductCompetitor;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class RNDProductCompetitorController extends Controller
{
    public function index()
    {
        try {
            $competitors = RndProductCompetitor::paginate(10);
            return response()->json($competitors);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch data', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'rnd_request_id' => 'required|exists:rnd_requests,id',
                'name' => 'required|string',
                'strength' => 'required|string',
                'dose' => 'required|string',
                'packaging' => 'required|string',
                'form' => 'required|string',
                'hna_target' => 'required|numeric',
            ]);

            $competitor = RndProductCompetitor::create($validated);
            return response()->json($competitor, Response::HTTP_CREATED);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'message' => $e->errors()], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to store data', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $competitor = RndProductCompetitor::findOrFail($id);
            return response()->json($competitor);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch data', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $competitor = RndProductCompetitor::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|string',
                'strength' => 'sometimes|string',
                'dose' => 'sometimes|string',
                'packaging' => 'sometimes|string',
                'form' => 'sometimes|string',
                'hna_target' => 'sometimes|numeric',
            ]);

            $competitor->update($validated);
            return response()->json($competitor);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update data', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            RndProductCompetitor::findOrFail($id)->delete();
            return response()->json(null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete data', 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
