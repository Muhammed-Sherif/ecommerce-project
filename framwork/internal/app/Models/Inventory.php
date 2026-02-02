<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;

class Inventory extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'inventory';
    protected $guarded = [];
    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
    ];
}
