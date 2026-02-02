<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;
use App\Models\User;

class Order extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'orders';
    protected $guarded = [];
    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
