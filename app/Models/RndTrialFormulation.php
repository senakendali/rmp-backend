<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndTrialFormulation extends Model
{
    protected $table = 'rnd_trial_formulations';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function rndRequest()
    {
        return $this->belongsTo(RndRequest::class);
    }

    public function rndTrialFormulationDetails()
    {
        return $this->hasMany(RndTrialFormulationDetail::class);
    }
}
