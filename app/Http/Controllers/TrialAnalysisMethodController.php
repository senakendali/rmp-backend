<?php

namespace App\Http\Controllers;

use App\Models\TrialAnalysisMethod;
use App\Models\TrialAnalysisMethodDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class TrialAnalysisMethodController extends Controller
{
    // Index: Get all trial analysis methods with their documents
    public function index()
    {
        $trialAnalysisMethods = TrialAnalysisMethod::with('documents')->get();

        return response()->json([
            'status' => 'success',
            'data' => $trialAnalysisMethods,
        ], 200);
    }

    // Show: Get a specific trial analysis method by ID with its documents
    public function show($id)
    {
        $trialAnalysisMethod = TrialAnalysisMethod::with('documents')->find($id);

        if (!$trialAnalysisMethod) {
            return response()->json([
                'status' => 'error',
                'message' => 'Trial analysis method not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $trialAnalysisMethod,
        ], 200);
    }

    // Store: Create a new trial analysis method and its documents

    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'rnd_request_id' => 'required|exists:rnd_requests,id',
            'rnd_trial_formulation_id' => 'required|exists:rnd_trial_formulations,id',
            'name' => 'required|string|max:255',
            'trial_date' => 'required|date',
            'documents' => 'required|array',
            'documents.*.raw_material_id' => 'required|exists:raw_materials,id',
            'documents.*.literature_document' => 'required|file|mimes:pdf,doc,docx|max:2048', // Validate file
            'documents.*.report_document' => 'required|file|mimes:pdf,doc,docx|max:2048', // Validate file
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction(); // Start a database transaction

        try {
            // Create the trial analysis method
            $trialAnalysisMethod = TrialAnalysisMethod::create([
                'rnd_request_id' => $request->rnd_request_id,
                'rnd_trial_formulation_id' => $request->rnd_trial_formulation_id,
                'name' => $request->name,
                'trial_date' => $request->trial_date,
            ]);

            // Ensure the documents directory exists
            $directory = 'public/trial_analysis_documents';
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory); // Create the directory if it doesn't exist
            }

            // Insert the documents
            foreach ($request->documents as $document) {
                // Store literature document
                $literatureDocumentPath = $document['literature_document']->store($directory);
                $literatureDocumentUrl = Storage::url($literatureDocumentPath);

                // Store report document
                $reportDocumentPath = $document['report_document']->store($directory);
                $reportDocumentUrl = Storage::url($reportDocumentPath);

                TrialAnalysisMethodDocument::create([
                    'trial_analysis_method_id' => $trialAnalysisMethod->id,
                    'raw_material_id' => $document['raw_material_id'],
                    'literature_document' => $literatureDocumentUrl, // Save file path
                    'report_document' => $reportDocumentUrl, // Save file path
                ]);
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => 'success',
                'message' => 'Trial analysis method and documents created successfully',
                'data' => $trialAnalysisMethod->load('documents'), // Load the related documents
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create trial analysis method',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Update: Update a trial analysis method and its documents
    public function update(Request $request, $id)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'rnd_request_id' => 'sometimes|exists:rnd_requests,id',
            'rnd_trial_formulation_id' => 'sometimes|exists:rnd_trial_formulations,id',
            'name' => 'sometimes|string|max:255',
            'trial_date' => 'sometimes|date',
            'documents' => 'sometimes|array',
            'documents.*.raw_material_id' => 'required|exists:raw_materials,id',
            'documents.*.literature_document' => 'sometimes|file|mimes:pdf,doc,docx|max:2048', // Validate file
            'documents.*.report_document' => 'sometimes|file|mimes:pdf,doc,docx|max:2048', // Validate file
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction(); // Start a database transaction

        try {
            // Find the trial analysis method
            $trialAnalysisMethod = TrialAnalysisMethod::find($id);

            if (!$trialAnalysisMethod) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Trial analysis method not found',
                ], 404);
            }

            // Update the trial analysis method
            $trialAnalysisMethod->update($request->only([
                'rnd_request_id',
                'rnd_trial_formulation_id',
                'name',
                'trial_date',
            ]));

            // Update or create documents
            if ($request->has('documents')) {
                // Ensure the documents directory exists
                $directory = 'public/trial_analysis_documents';
                if (!Storage::exists($directory)) {
                    Storage::makeDirectory($directory); // Create the directory if it doesn't exist
                }

                // Delete existing documents
                $trialAnalysisMethod->documents()->delete();

                // Insert new documents
                foreach ($request->documents as $document) {
                    // Store literature document
                    $literatureDocumentPath = $document['literature_document']->store($directory);
                    $literatureDocumentUrl = Storage::url($literatureDocumentPath);

                    // Store report document
                    $reportDocumentPath = $document['report_document']->store($directory);
                    $reportDocumentUrl = Storage::url($reportDocumentPath);

                    TrialAnalysisMethodDocument::create([
                        'trial_analysis_method_id' => $trialAnalysisMethod->id,
                        'raw_material_id' => $document['raw_material_id'],
                        'literature_document' => $literatureDocumentUrl, // Save file path
                        'report_document' => $reportDocumentUrl, // Save file path
                    ]);
                }
            }

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => 'success',
                'message' => 'Trial analysis method and documents updated successfully',
                'data' => $trialAnalysisMethod->load('documents'), // Load the related documents
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update trial analysis method',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Destroy: Delete a trial analysis method and its documents
    public function destroy($id)
    {
        DB::beginTransaction(); // Start a database transaction

        try {
            // Find the trial analysis method
            $trialAnalysisMethod = TrialAnalysisMethod::find($id);

            if (!$trialAnalysisMethod) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Trial analysis method not found',
                ], 404);
            }

            // Delete the trial analysis method and its documents (cascade delete)
            $trialAnalysisMethod->delete();

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => 'success',
                'message' => 'Trial analysis method and documents deleted successfully',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete trial analysis method',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}