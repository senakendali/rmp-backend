<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrialFormulaSpecification extends Model
{
    protected $table = 'trial_formula_specifications';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function rnd_trial_formulation()
    {
        return $this->belongsTo(RndTrialFormulation::class, 'rnd_trial_formulation_id');
    }
}
