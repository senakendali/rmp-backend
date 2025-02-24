<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RndProcessConfirmation extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'rnd_process_confirmations';

    public function rndRequest()
    {
        return $this->belongsTo(RndRequest::class, 'rnd_request_id');
    }

    public function rndProcessDetail(){
        return $this->belongsTo(RndProcessDetail::class, 'rnd_process_detail_id');
    }
}
