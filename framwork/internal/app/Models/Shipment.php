<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Shipment extends Model
{
    use BelongsToTenant;

    protected $table = 'shipments';
    protected $guarded = [];
    protected $casts = [
        'address' => 'array',
        'cost' => 'decimal:2',
    ];
}
