<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;

class Review extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'reviews';
    protected $guarded = [];
    protected $casts = [
        'rating' => 'integer',
    ];
}
