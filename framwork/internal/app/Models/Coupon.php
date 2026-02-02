<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;

class Coupon extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'coupons';
    protected $guarded = [];
    protected $casts = [
        'value' => 'float',
        'min_order_amount' => 'float',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];
}
