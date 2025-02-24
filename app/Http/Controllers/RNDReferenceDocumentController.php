<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\RndReferenceDocument;
use App\Models\RndRequest;
use Exception;

class RndReferenceDocumentController extends Controller
{
    /**
     * Menampilkan list berdasarkan rnd_request_id
     */
    public function index(Request $request, $rnd_request_id): JsonResponse
    {
        try {
            $query = RndReferenceDocument::query();

            if ($rnd_request_id) {
                $query->where('rnd_request_id', $rnd_request_id);
            }

            $documents = $query->get();

            return response()->json($documents, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan dokumen referensi dan memperbarui status RndRequest
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'rnd_request_id' => 'required|exists:rnd_requests,id',
            'name' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png', // Hanya menerima file PDF atau DOC
        ]);

        try {
            // Buat folder storage jika belum ada
            $folderPath = 'rnd_documents';
            Storage::makeDirectory($folderPath);

            // Simpan file di storage
            $file = $request->file('file');
            $filePath = $file->store($folderPath, 'public');

            // Simpan data di database
            $document = RndReferenceDocument::create([
                'rnd_request_id' => $request->rnd_request_id,
                'name' => $request->name,
                'file_path' => $filePath,
            ]);

            // Update status di RndRequest menjadi 'submit'
            RndRequest::where('id', $request->rnd_request_id)->update(['status' => 'submit']);

            return response()->json([
                'message' => 'Document uploaded successfully',
                'data' => $document
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Failed to upload document',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}

