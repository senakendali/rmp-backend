<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderAdjustmentNote extends Model
{
    protected $table = 'purchase_order_adjustment_notes';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
    
}
