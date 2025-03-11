<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RndTrialPackagingMaterial;
use App\Models\RndTrialPackagingMaterialDetail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RndTrialPackagingMaterialController extends Controller
{
    public function index()
    {
        try {
            return RndTrialPackagingMaterial::with('rndTrialPackagingMaterialDetails')->paginate(10);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rnd_request_id' => 'required|exists:rnd_requests,id',
            'trial_name' => 'required|string',
            'trial_date' => 'required|date',
            'procedure' => 'required|string',
            'details' => 'required|array',
            'details.*.goods_id' => 'required|exists:goods,id',
            'details.*.vendor_id' => 'required|exists:vendors,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            return DB::transaction(function () use ($request) {
                $trial = RndTrialPackagingMaterial::create([
                    'rnd_request_id' => $request->rnd_request_id,
                    'trial_name' => $request->trial_name,
                    'trial_date' => $request->trial_date,
                    'procedure' => $request->procedure,
                    'user_id' => auth()->id(), //Menggunakan ID user yang sedang login
                    'status' => 'Menunggu Persetujuan',
                ]);

                foreach ($request->details as $detail) {
                    $trial->rndTrialPackagingMaterialDetails()->create($detail);
                }

                return response()->json($trial->load('rndTrialPackagingMaterialDetails'), 201);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        try {
            return RndTrialPackagingMaterial::with('rndTrialPackagingMaterialDetails')->findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rnd_request_id' => 'required|exists:rnd_requests,id',
            'trial_name' => 'required|string',
            'trial_date' => 'required|date',
            'procedure' => 'required|string',
            'status' => 'nullable|in:Direvisi,Disetujui,Ditolak',
            'details' => 'required|array',
            'details.*.goods_id' => 'required|exists:goods,id',
            'details.*.vendor_id' => 'required|exists:vendors,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            return DB::transaction(function () use ($request, $id) {
                $trial = RndTrialPackagingMaterial::findOrFail($id);

                // Cek apakah user yang login adalah pemilik trial
                if ($trial->user_id !== auth()->id()) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }

                // Tentukan apakah status termasuk yang memerlukan persetujuan
                $status = $request->status;
                $isApprovalStatus = in_array($status, ['Direvisi', 'Disetujui', 'Ditolak']);

                // Update data utama tanpa mengubah user_id
                $trial->update([
                    'rnd_request_id' => $request->rnd_request_id,
                    'trial_name' => $request->trial_name,
                    'trial_date' => $request->trial_date,
                    'procedure' => $request->procedure,
                    'status' => $status ?? 'Menunggu Persetujuan',
                    'approved_by' => $isApprovalStatus ? auth()->id() : null,
                    'approved_date' => $isApprovalStatus ? now() : null,
                ]);

                // Hapus semua detail lama dan masukkan yang baru
                $trial->rndTrialPackagingMaterialDetails()->delete();
                foreach ($request->details as $detail) {
                    $trial->rndTrialPackagingMaterialDetails()->create($detail);
                }

                return response()->json($trial->load('rndTrialPackagingMaterialDetails'), 200);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }



    public function destroy($id)
    {
        try {
            RndTrialPackagingMaterial::destroy($id);
            return response()->json(['message' => 'Deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
