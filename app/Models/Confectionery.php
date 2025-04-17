<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Confectionery extends Model
{   
    // Tabela associada a este modelo
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'phone',
        'address_id',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Função responsável por definir a tabela associada a este modelo
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    // Função responsável por definir a relação entre Confectionery e Product
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
