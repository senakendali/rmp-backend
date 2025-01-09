<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderOfferCosts extends Model
{
    protected $table = 'purchase_order_offer_costs';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function purchaseOrderOffer()
    {
        return $this->belongsTo(PurchaseOrderOffer::class);
    }
}
