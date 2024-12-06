<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorsDocument extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'vendors_documents';

    public function vendor()
    {
        return $this->belongsTo(Vendors::class, 'vendors_id');
    }
}
