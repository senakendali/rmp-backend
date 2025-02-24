<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndProductSubstance extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'rnd_product_substances';

    public function rndProductDetail()
    {
        return $this->belongsTo(RndProductDetail::class, 'rnd_product_details_id');
    }
}
