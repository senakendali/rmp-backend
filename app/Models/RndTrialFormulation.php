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

    public function reports()
    {
        return $this->hasMany(TrialFormulaReport::class);
    }

    public function procedures()
    {
        return $this->hasMany(TrialFormulaProcedure::class);
    }

    public function specifications()
    {
        return $this->hasMany(TrialFormulaSpecification::class);
    }

    public function conclusion()
    {
        return $this->hasOne(TrialFormulaConclusion::class);
    }

    public function documents()
    {
        return $this->hasMany(TrialFormulaDocument::class);
    }

    public function trialAnalysisMethod()
    {
        return $this->hasOne(TrialAnalysisMethod::class);
    }
}
