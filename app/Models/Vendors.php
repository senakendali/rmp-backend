<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Vendors extends Model
{

    use HasFactory;

    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'vendors';
    protected $casts = [
        'goods_category' => 'array', // Menyesuaikan tipe data untuk sistem tags
    ];

    public function documents()
    {
        return $this->hasMany(VendorsDocument::class, 'vendors_id');
    }
    
    public function purchaseOrderOffers()
    {
        return $this->hasMany(PurchaseOrderOffer::class, 'vendor_id');
    }
}
