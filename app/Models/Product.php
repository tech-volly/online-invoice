<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_name',
        'product_slug',
        'product_description',
        'product_tax',
        'product_price',
        'product_purchase_price',
        'product_margin',
        'product_base_price',
        'product_gst_value',
        'product_final_price',
        'category_id',
        'is_status'
    ];
}
