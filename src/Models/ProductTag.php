<?php

namespace admin\tags\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductTag extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function products()
    {
        if (class_exists(\admin\products\Models\Product::class)) {
            return $this->belongsToMany(\admin\products\Models\Product::class, 'product_tags');
        }
    }
}
