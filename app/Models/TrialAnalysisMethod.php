<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrialAnalysisMethod extends Model
{
    protected $table = 'trial_analysis_methods';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function documents()
    {
        return $this->hasMany(TrialAnalysisMethodDocument::class);
    }

    public function trialFormulation()
    {
        return $this->belongsTo(RndTrialFormulation::class);
    }

    public function request()
    {    
        return $this->belongsTo(RndRequest::class);
    }
}
