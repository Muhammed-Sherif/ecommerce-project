<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use App\Models\Concerns\AppliesUserScope;
use App\Models\Inventory;
class Product extends Model
{
    use BelongsToTenant;
    use AppliesUserScope;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'description',
        'price',
        'images',
        'quantity',
        //'features',
        'status',
        'category'
    ];
    protected $guarded = [];
    protected $casts = [
        'images' => 'array',
        // 'features' => 'array',
        'price' => 'decimal:2',
    ];
    protected $appends = [
        'quantity',
        'image',
    ];
    public function inventory()
    {
        return $this->hasOne(Inventory::class, 'product_id', 'id');
    }

    public function getQuantityAttribute()
    {
        if ($this->relationLoaded('inventory') && $this->inventory) {
            return (int) $this->inventory->quantity;
        }

        return 0;
    }

    public function getImageAttribute()
    {
        if (is_array($this->images) && count($this->images) > 0) {
            return $this->images[0];
        }

        return null;
    }
    
}
