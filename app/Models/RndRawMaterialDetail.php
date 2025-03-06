<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndRawMaterialDetail extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'rnd_raw_material_details';

    public function rndRawMaterial()
    {
        return $this->belongsTo(RndRawMaterial::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
