<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\AppliesUserScope;

class Setting extends Model
{
    use AppliesUserScope;

    protected $table = 'settings';
    protected $guarded = [];

}
