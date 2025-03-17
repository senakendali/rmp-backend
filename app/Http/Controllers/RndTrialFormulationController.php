<?php

namespace App\Http\Controllers;

use App\Models\RndTrialFormulation;
use App\Models\RndTrialFormulationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class RndTrialFormulationController extends Controller
{
    public function index()
    {
        try {
            return RndTrialFormulation::with('rndTrialFormulationDetails')->paginate(10);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rnd_request_id' => 'required|exists:rnd_requests,id',
            'name' => 'required|string',
            'trial_date' => 'required|date',
            'details' => 'required|array',
            'details.*.raw_material_id' => 'required|exists:raw_materials,id',
            'details.*.vendor_id' => 'required|exists:vendors,id',
        ]);

        DB::beginTransaction();
        try {
           

            $formulation = RndTrialFormulation::create([
                'rnd_request_id' => $request->rnd_request_id,
                'name' => $request->name,
                'trial_date' => $request->trial_date,
                'user_id' => auth()->id(), //Menggunakan ID user yang sedang login
                'status' => 'Menunggu Persetujuan',
            ]);

            foreach ($data['details'] as $detail) {
                $formulation->rndTrialFormulationDetails()->create($detail);
            }
            DB::commit();
            return response()->json($formulation->load('rndTrialFormulationDetails'), 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show($id)
    {
        return response()->json(
            RndTrialFormulation::with([
                'rndTrialFormulationDetails', // Eager load the first relationship
                'rndTrialFormulationDetails.rawMaterial' // Eager load the nested relationship
            ])->findOrFail($id)
        );
    }

    public function update(Request $request, $id)
    {
        $formulation = RndTrialFormulation::findOrFail($id);
        $data = $request->validate([
            'name' => 'string',
            'trial_date' => 'date',
            'details' => 'array',
            'details.*.id' => 'nullable|exists:rnd_trial_formulation_details,id',
            'details.*.raw_material_id' => 'required_with:details|exists:raw_materials,id',
            'details.*.vendor_id' => 'required_with:details|exists:vendors,id',
        ]);

        DB::beginTransaction();
        try {
            $formulation->update($data);
            if (isset($data['details'])) {
                foreach ($data['details'] as $detail) {
                    if (isset($detail['id'])) {
                        RndTrialFormulationDetail::where('id', $detail['id'])->update($detail);
                    } else {
                        $formulation->rndTrialFormulationDetails()->create($detail);
                    }
                }
            }
            DB::commit();
            return response()->json($formulation->load('rndTrialFormulationDetails'));
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            RndTrialFormulation::destroy($id);
            return response()->json(['message' => 'Deleted successfully']);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
