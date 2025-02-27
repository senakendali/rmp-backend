<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndRequest extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'rnd_requests';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function rndProductCompetitors()
    {
        return $this->hasMany(RndProductCompetitor::class);
    }

    public function rndProductDetails()
    {
        return $this->hasMany(RndProductDetail::class);
    }

    public function rndReferenceDocuments()
    {
        return $this->hasMany(RndReferenceDocument::class);
    }

    public function rndProductSubstances()
    {
        return $this->hasMany(RndProductSubstance::class, 'rnd_request_id');
    }
}
