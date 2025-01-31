<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcurementLog extends Model
{
    protected $table = 'procurement_logs';

    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function purchaseOrderOffer()
    {
        return $this->belongsTo(PurchaseOrderOffer::class);
    }

    public function purchaseOrderParticipant()
    {
        return $this->belongsTo(PurchaseOrderParticipant::class);
    }

   


}
