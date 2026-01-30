<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Review extends Model
{
    use BelongsToTenant;

    protected $table = 'reviews';
    protected $guarded = [];
    protected $casts = [
        'rating' => 'integer',
    ];
}
