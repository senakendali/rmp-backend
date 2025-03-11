<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndTrialPackagingMaterialDetail extends Model
{
    protected $table = 'rnd_trial_packaging_material_details';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function rndTrialPackagingMaterial()
    {
        return $this->belongsTo(RndTrialPackagingMaterial::class, 'rnd_trial_pm_id', 'id');
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class);
    }

    public function vendors()
    {
        return $this->belongsTo(Vendors::class);
    }
}
