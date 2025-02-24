<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndProcessDetail extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'rnd_process_details';

    public function rndProcess()
    {
        return $this->belongsTo(RndProcess::class, 'rnd_process_id');
    }

    public function rndProcessConfirmation()
    {
        return $this->hasMany(RndProcessConfirmation::class, 'rnd_process_detail_id');
    }
}
