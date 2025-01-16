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
        return $this->belongsTo(Vendors::class, 'vendor_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(PurchaseOrderOfferItems::class, 'purchase_order_offer_id');
    }

    public function purchaseOrderCosts()
    {
        return $this->hasMany(PurchaseOrderOfferCosts::class, 'purchase_order_offer_id');
    }

    public function purchaseOrderPayments()
    {
        return $this->hasMany(PurchaseOrderPayment::class, 'purchase_order_offer_id');
    }
    
}
