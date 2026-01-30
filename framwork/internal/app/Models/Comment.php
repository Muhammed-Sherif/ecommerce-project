<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Comment extends Model
{
    use BelongsToTenant;

    protected $table = 'comments';
    protected $guarded = [];
}
