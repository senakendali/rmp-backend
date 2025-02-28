<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RndProcessDocumentation;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RndProcessDocumentationController extends Controller
{
    public function index(Request $request)
    {
        $query = RndProcessDocumentation::query();

        if ($request->has('rnd_request_id')) {
            $query->where('rnd_request_id', $request->rnd_request_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rnd_request_id' => 'required|exists:rnd_requests,id',
            'rnd_process_detail_id' => 'required|exists:rnd_process_details,id',
            'document_name' => 'required|string|max:255',
            'document_file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Buat folder qbd_documents jika belum ada
        $folder = 'qbd_documents';
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }

        $file = $request->file('document_file');
        $path = $file->store($folder, 'public'); // Simpan file di folder qbd_documents

        $doc = RndProcessDocumentation::create([
            'rnd_request_id' => $request->rnd_request_id,
            'rnd_process_detail_id' => $request->rnd_process_detail_id,
            'document_name' => $request->document_name,
            'document_path' => $path,
        ]);

        return response()->json($doc, 201);
    }


    public function show($id)
    {
        $doc = RndProcessDocumentation::findOrFail($id);
        return response()->json($doc);
    }

    public function update(Request $request, $id)
    {
        $doc = RndProcessDocumentation::findOrFail($id);
    
        $validator = Validator::make($request->all(), [
            'document_name' => 'sometimes|string|max:255',
            'document_file' => 'sometimes|file|mimes:pdf,doc,docx,jpg,png|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Buat folder qbd_documents kalau belum ada
        $folder = 'qbd_documents';
        if (!Storage::disk('public')->exists($folder)) {
            Storage::disk('public')->makeDirectory($folder);
        }
    
        // Update document name
        if ($request->has('document_name')) {
            $doc->document_name = $request->input('document_name');
        }
    
        // Update document file
        if ($request->hasFile('document_file')) {
            // Hapus file lama kalau ada
            if ($doc->document_path && Storage::disk('public')->exists($doc->document_path)) {
                Storage::disk('public')->delete($doc->document_path);
            }
    
            // Upload file baru
            $file = $request->file('document_file');
            $path = $file->store($folder, 'public');
            $doc->document_path = $path;
        }
    
        $doc->save();
    
        return response()->json($doc);
    }
    




    public function destroy($id)
    {
        $doc = RndProcessDocumentation::findOrFail($id);
        Storage::disk('public')->delete($doc->document_path);
        $doc->delete();

        return response()->json(['message' => 'Dokumentasi berhasil dihapus'], 200);
    }
}
