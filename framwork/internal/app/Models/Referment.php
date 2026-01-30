<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class Referment extends Model
{
    use BelongsToTenant;

    protected $table = 'referments';
    protected $guarded = [];
}
