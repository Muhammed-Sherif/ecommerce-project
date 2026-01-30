<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Inventory extends Model
{
    use BelongsToTenant;

    protected $table = 'inventory';
    protected $guarded = [];
    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
    ];
}
