<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;

class CartItem extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'cart_items';
    protected $guarded = [];
    protected $casts = [
        'quantity' => 'integer',
    ];
}
