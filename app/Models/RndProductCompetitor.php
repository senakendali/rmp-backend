<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndProductCompetitor extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'rnd_product_competitors';

    public function rndRequest()
    {
        return $this->belongsTo(RndRequest::class);
    }
}
