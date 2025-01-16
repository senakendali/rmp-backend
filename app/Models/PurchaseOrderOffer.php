<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderOffer extends Model
{
    protected $table = 'purchase_order_offers';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendors::class);
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderOfferItems::class);
    }

    public function purchaseOrderCosts()
    {
        return $this->hasMany(PurchaseOrderOfferCosts::class);
    }

    public function purchaseOrderPayments()
    {
        return $this->hasMany(PurchaseOrderPayment::class);
    }
    
}
