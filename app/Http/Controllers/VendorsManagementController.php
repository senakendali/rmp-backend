<?php

namespace App\Http\Controllers;

use App\Models\Vendors;
use App\Models\VendorsDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class VendorsManagementController extends Controller
{
    public function __construct()
    {
        ini_set('upload_max_filesize', '10M');
        ini_set('post_max_size', '10M');
    }
    
    /**
     * Display a listing of the vendors.
     */
    public function index()
    {
        $vendors = Vendors::with('documents')->paginate(10);
        return response()->json($vendors);
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Validation for vendor
            'name' => 'required|string|max:255',
            'goods_category' => 'nullable|array',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:20',
            'pic_email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'verification_status' => 'required|in:verified,unverified',

            // Validation for documents
            'documents' => 'nullable|array',
            'documents.*.file' => 'required_with:documents|file|mimes:pdf,jpg,png|max:10240',
            'documents.*.description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Create the vendor
            $vendor = Vendors::create([
                'name' => $validated['name'],
                'goods_category' => $validated['goods_category'] ?? [],
                'pic_name' => $validated['pic_name'],
                'pic_phone' => $validated['pic_phone'],
                'pic_email' => $validated['pic_email'],
                'address' => $validated['address'],
                'status' => $validated['status'],
                'verification_status' => $validated['verification_status'],
            ]);

            // Handle document uploads
            if (!empty($validated['documents'])) {
                foreach ($validated['documents'] as $document) {
                    // Upload the file
                    $filePath = $document['file']->store('vendor_documents', 'public');

                    // Insert document record into the database
                    VendorsDocument::create([
                        'vendors_id' => $vendor->id,
                        'file_name' => $filePath,
                        'description' => $document['description'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Vendor and documents created successfully.',
                'vendor' => $vendor->load('documents'), // Load related documents
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create vendor and documents.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified vendor.
     */
    public function show($id)
    {
        $vendor = Vendors::with('documents')->findOrFail($id);
        return response()->json($vendor);
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(Request $request, Vendors $vendor)
    {
        $validated = $request->validate([
            // Validation for vendor
            'name' => 'required|string|max:255',
            'goods_category' => 'nullable|array',
            'pic_name' => 'required|string|max:255',
            'pic_phone' => 'required|string|max:20',
            'pic_email' => 'required|email|max:255',
            'address' => 'required|string|max:255',
            'status' => 'required|in:active,inactive',
            'verification_status' => 'required|in:verified,unverified',

            // Validation for documents
            'documents' => 'nullable|array',
            'documents.*.id' => 'nullable|exists:vendors_documents,id',
            'documents.*.file' => 'nullable|file|mimes:pdf,jpg,png|max:10240',
            'documents.*.description' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            // Update vendor details
            $vendor->update([
                'name' => $validated['name'],
                'goods_category' => $validated['goods_category'] ?? [],
                'pic_name' => $validated['pic_name'],
                'pic_phone' => $validated['pic_phone'],
                'pic_email' => $validated['pic_email'],
                'address' => $validated['address'],
                'status' => $validated['status'],
                'verification_status' => $validated['verification_status'],
            ]);

            // Handle document updates
            if (!empty($validated['documents'])) {
                foreach ($validated['documents'] as $document) {
                    if (isset($document['id'])) {
                        // Update existing document
                        $vendorDocument = VendorsDocument::find($document['id']);
                        if ($vendorDocument) {
                            // If a new file is provided, delete the old one and upload the new file
                            if (isset($document['file'])) {
                                // Delete old file
                                if (Storage::exists($vendorDocument->file_name)) {
                                    Storage::delete($vendorDocument->file_name);
                                }

                                // Upload new file
                                $filePath = $document['file']->store('vendor_documents', 'public');
                                $vendorDocument->update([
                                    'file_name' => $filePath,
                                ]);
                            }

                            // Update description
                            $vendorDocument->update([
                                'description' => $document['description'] ?? $vendorDocument->description,
                            ]);
                        }
                    } else {
                        // Add a new document
                        $filePath = $document['file']->store('vendor_documents', 'public');
                        VendorsDocument::create([
                            'vendors_id' => $vendor->id,
                            'file_name' => $filePath,
                            'description' => $document['description'] ?? null,
                        ]);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Vendor and documents updated successfully.',
                'vendor' => $vendor->load('documents'), // Load related documents
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to update vendor and documents.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified vendor from storage.
     */
    public function destroy($id)
    {
        $vendor = Vendors::findOrFail($id);
        $vendor->delete();

        return response()->json(['message' => 'Vendor deleted successfully.'], 204);
    }

    /**
     * Add a document to the specified vendor.
     */
    public function addDocument(Request $request, $vendorId)
    {
        $validated = $request->validate([
            'file_name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $document = VendorsDocument::create([
            'vendors_id' => $vendorId,
            'file_name' => $validated['file_name'],
            'description' => $validated['description'],
        ]);

        return response()->json(['message' => 'Document added successfully.', 'data' => $document], 201);
    }

    /**
     * Delete a document.
     */
    public function deleteDocument($id)
    {
        $document = VendorsDocument::findOrFail($id);
        $document->delete();

        return response()->json(['message' => 'Document deleted successfully.'], 204);
    }
}
