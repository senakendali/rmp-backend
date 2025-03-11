<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndTrialPackagingMaterial extends Model
{
    protected $table = 'rnd_trial_packaging_materials';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function rndTrialPackagingMaterialDetails()
    {
        return $this->hasMany(RndTrialPackagingMaterialDetail::class, 'rnd_trial_pm_id', 'id');
    }

    public function rndRequest()
    {
        return $this->belongsTo(RndRequest::class);
    }
}
