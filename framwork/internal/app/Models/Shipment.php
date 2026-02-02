<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;

class Shipment extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'shipments';
    protected $guarded = [];
    protected $casts = [
        'address' => 'array',
        'cost' => 'decimal:2',
    ];
}
