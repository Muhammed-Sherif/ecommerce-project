<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CartItem extends Model
{
    use BelongsToTenant;

    protected $table = 'cart_items';
    protected $guarded = [];
    protected $casts = [
        'quantity' => 'integer',
    ];
}
