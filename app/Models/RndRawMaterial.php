<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndRawMaterial extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'rnd_raw_materials';

    public function rndRawMaterialDetails()
    {
        return $this->hasMany(RndRawMaterialDetail::class);
    }


}
