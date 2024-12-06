<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendors extends Model
{
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
}
