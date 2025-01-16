<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderPaymentRecord extends Model
{
    protected $table = 'purchase_order_payment_records';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function purchaseOrderPayment()
    {
        return $this->belongsTo(PurchaseOrderPayment::class, 'purchase_order_payment_id');
    }
}
