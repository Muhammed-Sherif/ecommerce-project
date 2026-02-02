<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;

class OrderItem extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'order_items';
    protected $guarded = [];
    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quantity' => 'integer',
    ];
}
