<?php

//php artisan test --testdox

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Vendors;
use App\Models\VendorsDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class VendorsManagementControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_can_list_vendors()
    {
        $vendors = Vendors::factory()->count(3)->create();

        $response = $this->getJson('/api/vendors');

        $response->assertStatus(200)
                ->assertJsonCount(3, 'data');
    }

    public function test_can_create_vendor()
    {
        $vendorData = [
            'name' => 'New Vendor',
            'goods_category' => ['category1', 'category2'],
            'pic_name' => 'John Doe',
            'pic_phone' => '1234567890',
            'pic_email' => 'john@example.com',
            'address' => 'Test Address',
            'status' => 'active',
            'verification_status' => 'verified',
            'documents' => [
                [
                    'file' => UploadedFile::fake()->create('document.pdf', 1000),
                    'description' => 'Test Document'
                ]
            ]
        ];

        $response = $this->postJson('/api/vendors', $vendorData);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Vendor and documents created successfully.'
                ]);

        $this->assertDatabaseHas('vendors', [
            'name' => 'New Vendor',
            'pic_email' => 'john@example.com'
        ]);
    }

    public function test_can_show_vendor()
    {
        $vendor = Vendors::factory()->create();

        $response = $this->getJson("/api/vendors/{$vendor->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $vendor->id,
                    'name' => $vendor->name
                ]);
    }

    public function test_can_update_vendor()
    {
        $vendor = Vendors::factory()->create();

        $updateData = [
            'name' => 'Updated Vendor',
            'goods_category' => ['new_category'],
            'pic_name' => 'Jane Doe',
            'pic_phone' => '9876543210',
            'pic_email' => 'jane@example.com',
            'address' => 'New Address',
            'status' => 'inactive',
            'verification_status' => 'unverified'
        ];

        $response = $this->putJson("/api/vendors/{$vendor->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('vendors', [
            'id' => $vendor->id,
            'name' => 'Updated Vendor'
        ]);
    }

    public function test_can_delete_vendor()
    {
        $vendor = Vendors::factory()->create();

        $response = $this->deleteJson("/api/vendors/{$vendor->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('vendors', [
            'id' => $vendor->id
        ]);
    }

    public function test_validates_required_fields()
    {
        $response = $this->postJson('/api/vendors', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'name',
                    'pic_name',
                    'pic_phone',
                    'pic_email',
                    'address',
                    'status',
                    'verification_status'
                ]);
    }


    public function test_can_add_document()
    {
        $vendor = Vendors::factory()->create();

        $documentData = [
            'file_name' => 'test_document.pdf',
            'description' => 'Test document description'
        ];

        $response = $this->postJson("/api/vendors/{$vendor->id}/documents", $documentData);

        $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Document added successfully.',
                    'data' => [
                        'vendors_id' => $vendor->id,
                        'file_name' => 'test_document.pdf',
                        'description' => 'Test document description'
                    ]
                ]);

        $this->assertDatabaseHas('vendors_documents', [
            'vendors_id' => $vendor->id,
            'file_name' => 'test_document.pdf',
            'description' => 'Test document description'
        ]);
    }

    public function test_add_document_validates_required_fields()
    {
        $vendor = Vendors::factory()->create();

        $response = $this->postJson("/api/vendors/{$vendor->id}/documents", []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['file_name']);
    }

    public function test_add_document_validates_string_fields()
    {
        $vendor = Vendors::factory()->create();

        $response = $this->postJson("/api/vendors/{$vendor->id}/documents", [
            'file_name' => ['invalid_type'],
            'description' => ['invalid_type']
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['file_name', 'description']);
    }

    public function test_can_delete_document()
    {
        $vendor = Vendors::factory()->create();
        $document = VendorsDocument::create([
            'vendors_id' => $vendor->id,
            'file_name' => 'test_document.pdf',
            'description' => 'Test description'
        ]);

        $response = $this->deleteJson("/api/vendors/documents/{$document->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('vendors_documents', [
            'id' => $document->id
        ]);
    }

    public function test_delete_document_returns_404_for_non_existent_document()
    {
        $nonExistentId = 99999;

        $response = $this->deleteJson("/api/vendors/documents/{$nonExistentId}");

        $response->assertStatus(404);
    }
}
