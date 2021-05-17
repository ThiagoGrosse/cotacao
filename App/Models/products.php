<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $table = 'product';
    protected $fillable = ['id_item', 'sku_core', 'price', 'deadline'];
    // public $timestamps = false;
}
