<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingCompany extends Model
{
    protected $table = 'tb_shipping_company';
    protected $fillable = ['starting_track', 'final_track', 'zone', 'state', 'charge', 'shipping_company_name'];
    protected $dateFormat = 'Y-m-d H:i:sO';
}
