<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class StockMovement extends Model
{
    use BelongsToTenant;

    protected $table = 'stock_movements';
    protected $guarded = [];
}
