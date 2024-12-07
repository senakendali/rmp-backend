<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsCategories extends Model
{
    protected $guarded = ['created_at', 'updated_at'];
    protected $dates = ['created_at', 'updated_at'];
    protected $table = 'goods_category';

    public function goods()
    {
        return $this->hasMany(Goods::class, 'goods_category_id');
    }

}
