<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\AppliesUserScope;

class NewsletterSubscriber extends Model
{
    use AppliesUserScope;

    protected $fillable = [
        'email',
        'status',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];
}
