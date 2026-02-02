<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;

class Comment extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'comments';
    protected $guarded = [];
}
