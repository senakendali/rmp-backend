<?php

namespace App\Http\Controllers;

use App\Models\TrialFormulaReport;
use App\Models\TrialFormulaProcedure;
use App\Models\TrialFormulaSpecification;
use App\Models\TrialFormulaConclusion;
use App\Models\TrialFormulaDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrialFormulaController extends Controller
{
    public function index()
    {
        return response()->json([
            'reports' => TrialFormulaReport::all(),
            'procedures' => TrialFormulaProcedure::all(),
            'specifications' => TrialFormulaSpecification::all(),
            'conclusions' => TrialFormulaConclusion::all(),
            'documents' => TrialFormulaDocument::paginate(10),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'rnd_trial_formulation_id' => 'required|exists:rnd_trial_formulations,id',
            'reports' => 'required|array',
            'reports.*.raw_material_id' => 'required|exists:raw_materials,id',
            'reports.*.percentage' => 'required|numeric',
            'reports.*.mi' => 'required|numeric',
            'reports.*.smallest_unit' => 'required|numeric',
            'reports.*.weight' => 'required|numeric',

            'procedures' => 'required|array',
            'procedures.*.procedure' => 'required|string',

            'specifications' => 'required|array',
            'specifications.*.quality_parameter' => 'required|string',
            'specifications.*.condition' => 'required|string',
            'specifications.*.result' => 'required|string',

            'conclusion' => 'required|array',
            'conclusion.*.text' => 'required|string',

            'documents' => 'nullable|array',
            'documents.*.file' => 'required|file|mimes:pdf,doc,docx,xlsx|max:5120',
            'documents.*.description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            TrialFormulaReport::insert(array_map(fn($report) => [
                'rnd_trial_formulation_id' => $request->rnd_trial_formulation_id,
                'raw_material_id' => $report['raw_material_id'],
                'percentage' => $report['percentage'],
                'mi' => $report['mi'],
                'smallest_unit' => $report['smallest_unit'],
                'weight' => $report['weight'],
                'created_at' => now(),
                'updated_at' => now(),
            ], $request->reports));

            TrialFormulaProcedure::insert(array_map(fn($procedure) => [
                'rnd_trial_formulation_id' => $request->rnd_trial_formulation_id,
                'procedure' => $procedure['procedure'],
                'created_at' => now(),
                'updated_at' => now(),
            ], $request->procedures));

            TrialFormulaSpecification::insert(array_map(fn($spec) => [
                'rnd_trial_formulation_id' => $request->rnd_trial_formulation_id,
                'quality_parameter' => $spec['quality_parameter'],
                'condition' => $spec['condition'],
                'result' => $spec['result'],
                'created_at' => now(),
                'updated_at' => now(),
            ], $request->specifications));

            TrialFormulaConclusion::insert(array_map(fn($conclusion) => [
                'rnd_trial_formulation_id' => $request->rnd_trial_formulation_id,
                'conclusion' => $conclusion['text'],
                'created_at' => now(),
                'updated_at' => now(),
            ], $request->conclusion));

            if (!empty($request->documents)) {
                foreach ($request->documents as $document) {
                    $filePath = $document['file']->store('rnd_documents');
                    TrialFormulaDocument::create([
                        'rnd_trial_formulation_id' => $request->rnd_trial_formulation_id,
                        'file_name' => $filePath,
                        'description' => $document['description'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Data inserted successfully'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function show($id)
    {
        return response()->json([
            'reports' => TrialFormulaReport::with('raw_material')->where('rnd_trial_formulation_id', $id)->get(),
            'procedures' => TrialFormulaProcedure::where('rnd_trial_formulation_id', $id)->get(),
            'specifications' => TrialFormulaSpecification::where('rnd_trial_formulation_id', $id)->get(),
            'conclusions' => TrialFormulaConclusion::where('rnd_trial_formulation_id', $id)->get(),
            'documents' => TrialFormulaDocument::where('rnd_trial_formulation_id', $id)->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'rnd_trial_formulation_id' => 'required|exists:rnd_trial_formulations,id',
            'reports' => 'nullable|array',
            'reports.*.raw_material_id' => 'required|exists:raw_materials,id',
            'reports.*.percentage' => 'required|numeric',
            'reports.*.mi' => 'required|numeric',
            'reports.*.smallest_unit' => 'required|numeric',
            'reports.*.weight' => 'required|numeric',

            'procedures' => 'nullable|array',
            'procedures.*.procedure' => 'required|string',

            'specifications' => 'nullable|array',
            'specifications.*.quality_parameter' => 'required|string',
            'specifications.*.condition' => 'required|string',
            'specifications.*.result' => 'required|string',

            'conclusion' => 'nullable|array',
            'conclusion.*.text' => 'required|string',

            'documents' => 'nullable|array',
            'documents.*.file' => 'required|file|mimes:pdf,doc,docx,xlsx|max:5120',
            'documents.*.description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            if (!empty($request->documents)) {
                foreach ($request->documents as $document) {
                    $filePath = $document['file']->store('rnd_documents');
                    TrialFormulaDocument::create([
                        'rnd_trial_formulation_id' => $id,
                        'file_name' => $filePath,
                        'description' => $document['description'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json(['message' => 'Data updated successfully'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                TrialFormulaReport::where('rnd_trial_formulation_id', $id)->delete();
                TrialFormulaProcedure::where('rnd_trial_formulation_id', $id)->delete();
                TrialFormulaSpecification::where('rnd_trial_formulation_id', $id)->delete();
                TrialFormulaConclusion::where('rnd_trial_formulation_id', $id)->delete();
            });
            return response()->json(['message' => 'Data deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
