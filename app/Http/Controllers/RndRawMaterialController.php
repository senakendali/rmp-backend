<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RndRawMaterial;
use App\Models\RndRawMaterialDetail;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RndRawMaterialController extends Controller
{
    public function index(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            $rawMaterials = RndRawMaterial::with('rndRawMaterialDetails.rawMaterial')->paginate($perPage);
            return response()->json($rawMaterials);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch data', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'raw_material_type' => 'required|in:Bahan Aktif,Bahan Tambahan,Bahan Penolong',
            'raw_material_name' => 'required|string',
            'details' => 'required|array',
            'details.*.material_status' => 'required|in:Baru,Tersedia',
            'details.*.category' => 'required_if:details.*.material_status,Baru|in:Bahan Baku Ekstrak,Bahan Baku Awal (Aktif),Bahan Baku Awal (Non-Aktif),Bahan Baku Mentah,Bahan Kemas (Primer),Bahan Pelarut',
            'details.*.material_category' => 'required_if:details.*.material_status,Baru|in:Non Ekstraksi,Bahan Ekstraksi',
            'details.*.raw_material_id' => 'nullable|integer',
            'details.*.raw_material_code' => 'required|string',
            'details.*.raw_material_name' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $rawMaterial = RndRawMaterial::create([
                'raw_material_type' => $request->raw_material_type,
                'raw_material_name' => $request->raw_material_name,
            ]);

            foreach ($request->details as $detail) {
                if ($detail['material_status'] === 'Baru') {
                    $masterMaterial = RawMaterial::create([
                        'raw_material_code' => $detail['raw_material_code'],
                        'raw_material_name' => $detail['raw_material_name'],
                        'category' => $detail['category'],
                        'material_category' => $detail['material_category'],
                    ]);

                    $rawMaterialId = $masterMaterial->id;
                } else {
                    $rawMaterialId = $detail['raw_material_id'];
                }

                RndRawMaterialDetail::create([
                    'rnd_raw_material_id' => $rawMaterial->id,
                    'raw_material_id' => $rawMaterialId,
                    'material_status' => $detail['material_status'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Raw materials added successfully'], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to add raw materials', 'error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        $rawMaterial = RndRawMaterial::with('rndRawMaterialDetails.rawMaterial')->find($id);
        if (!$rawMaterial) {
            return response()->json(['message' => 'Raw material not found'], 404);
        }
        return response()->json($rawMaterial);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'raw_material_type' => 'required|in:Bahan Aktif,Bahan Tambahan,Bahan Penolong',
            'raw_material_name' => 'required|string',
            'raw_material_unit' => 'nullable|string',
            'stock' => 'nullable|integer',
        ]);

        $rawMaterial = RndRawMaterial::find($id);
        if (!$rawMaterial) {
            return response()->json(['message' => 'Raw material not found'], 404);
        }

        $rawMaterial->update($request->all());
        return response()->json(['message' => 'Raw material updated successfully']);
    }

    public function destroy($id)
    {
        $rawMaterial = RndRawMaterial::find($id);
        if (!$rawMaterial) {
            return response()->json(['message' => 'Raw material not found'], 404);
        }

        $rawMaterial->delete();
        return response()->json(['message' => 'Raw material deleted successfully']);
    }
}





