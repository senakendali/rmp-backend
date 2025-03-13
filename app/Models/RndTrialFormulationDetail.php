<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndTrialFormulationDetail extends Model
{
    protected $table = 'rnd_trial_formulation_details';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function rndTrialFormulation()
    {
        return $this->belongsTo(RndTrialFormulation::class);
    }
}
