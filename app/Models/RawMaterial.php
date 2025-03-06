<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'raw_materials';

    public function rawMaterialDetails()
    {
        return $this->hasMany(RawMaterialDetail::class);
    }
}
