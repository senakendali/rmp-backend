<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndReferenceDocument extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'rnd_reference_documents';

    public function rndRequest()
    {
        return $this->belongsTo(RndRequest::class);
    }
}
