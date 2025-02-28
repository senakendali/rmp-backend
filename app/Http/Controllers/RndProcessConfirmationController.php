<?php

namespace App\Http\Controllers;

use App\Models\RndProcessConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RndProcessConfirmationController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = RndProcessConfirmation::query();

            if ($request->has('rnd_request_id')) {
                $query->where('rnd_request_id', $request->rnd_request_id);
            }

            $confirmations = $query->get();
            return response()->json($confirmations);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rnd_request_id' => 'required|exists:rnd_requests,id',
            'rnd_process_detail_id' => 'required|exists:rnd_process_details,id',
            'confirmation' => 'required|in:Alihkan,Tolak Permintaan Pengalihan,Konfirmasi Proses',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $confirmation = RndProcessConfirmation::create($request->all());
            return response()->json($confirmation, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            $confirmation = RndProcessConfirmation::findOrFail($id);
            return response()->json($confirmation);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'confirmation' => 'required|in:Alihkan,Tolak Permintaan Pengalihan,Konfirmasi Proses',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $confirmation = RndProcessConfirmation::findOrFail($id);
            $confirmation->update($request->all());
            return response()->json($confirmation);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $confirmation = RndProcessConfirmation::findOrFail($id);
            $confirmation->delete();
            return response()->json(['message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
