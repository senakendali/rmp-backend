<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderParticipant extends Model
{
    protected $table = 'purchase_order_participants';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendors::class, 'vendor_id');
    }

    public function userCreated()
    {
        return $this->belongsTo(User::class, 'user_created');
    }

    public function userUpdated()
    {
        return $this->belongsTo(User::class, 'user_updated');
    }
}
