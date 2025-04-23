<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'confectionery_id'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    public function confectionery(): BelongsTo
    {
        return $this->belongsTo(Confectionery::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}