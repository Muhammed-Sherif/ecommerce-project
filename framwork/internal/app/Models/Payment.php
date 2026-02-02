<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;

class Payment extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'payments';
    protected $guarded = [];
    protected $casts = [
        'amount' => 'decimal:2',
    ];
}
