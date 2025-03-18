<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrialAnalysisMethodDocument extends Model
{
    protected $table = 'trial_analysis_method_documents';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function trialAnalysisMethod()
    {
        return $this->belongsTo(TrialAnalysisMethod::class);
    }

    public function rawMaterial()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
