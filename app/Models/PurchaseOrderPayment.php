<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderPayment extends Model
{
    protected $table = 'purchase_order_payments';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];


    public function purchaseOrderOffer()
    {
        return $this->belongsTo(PurchaseOrderOffer::class);
    }

    public function paymentRecords()
    {
        return $this->hasMany(PurchaseOrderPaymentRecord::class);
    }

}
