<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;

class StockMovement extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'stock_movements';
    protected $guarded = [];
}
