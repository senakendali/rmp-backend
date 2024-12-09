<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Vendors;
use App\Models\VendorsDocument;
use Illuminate\Foundation\Testing\RefreshDatabase;

class VendorsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_vendor()
    {
        $vendorData = [
            'name' => 'Test Vendor',
            'goods_category' => ['category1', 'category2'],
            'pic_name' => 'John Doe',
            'pic_phone' => '1234567890',
            'pic_email' => 'john@example.com',
            'address' => 'Test Address',
            'status' => 'active',
            'verification_status' => 'verified'
        ];

        $vendor = Vendors::create($vendorData);

        $this->assertDatabaseHas('vendors', [
            'name' => 'Test Vendor',
            'pic_email' => 'john@example.com'
        ]);

        $this->assertEquals($vendorData['goods_category'], $vendor->goods_category);
    }

    public function test_vendor_has_documents_relationship()
    {
        $vendor = Vendors::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $vendor->documents);
    }

    public function test_can_access_vendor_attributes()
    {
        $vendor = Vendors::create([
            'name' => 'Test Vendor',
            'goods_category' => ['category1'],
            'pic_name' => 'John Doe',
            'pic_phone' => '1234567890',
            'pic_email' => 'john@example.com',
            'address' => 'Test Address',
            'status' => 'active',
            'verification_status' => 'verified'
        ]);

        $this->assertEquals('Test Vendor', $vendor->name);
        $this->assertEquals(['category1'], $vendor->goods_category);
        $this->assertEquals('active', $vendor->status);
    }
}
