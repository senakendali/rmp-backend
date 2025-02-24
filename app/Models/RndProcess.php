<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndProcess extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'rnd_processes';

    public function rndProcessDetails()
    {
        return $this->hasMany(RndProcessDetail::class, 'rnd_process_id');
    }
}
