<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'goods';

    public function category()
    {
        return $this->belongsTo(GoodsCategories::class, 'goods_category_id');
    }
}
