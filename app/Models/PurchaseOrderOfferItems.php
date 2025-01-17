<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderOfferItems extends Model
{
    protected $table = 'purchase_order_offer_items';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function purchaseOrderOffer()
    {
        return $this->belongsTo(PurchaseOrderOffer::class);
    }

    

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class, 'po_item_id');
    }
}
