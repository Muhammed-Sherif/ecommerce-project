<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Payment extends Model
{
    use BelongsToTenant;

    protected $table = 'payments';
    protected $guarded = [];
    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
