<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $table = 'purchase_order_items';
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchaseRequestItem()
    {
        return $this->belongsTo(PurchaseRequestItem::class);
    }

    public function goods()
    {
        return $this->belongsTo(Goods::class, 'goods_id');
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class, 'measurement_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }
}
