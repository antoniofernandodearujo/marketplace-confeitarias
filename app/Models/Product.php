<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modelo de Produto
 * 
 * Representa os produtos oferecidos pelas confeitarias,
 * incluindo seus detalhes e relações com imagens.
 * 
 * @property int $id
 * @property string $name Nome do produto
 * @property float $price Preço do produto
 * @property string $description Descrição detalhada do produto
 * @property int $confectionery_id ID da confeitaria relacionada
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Confectionery $confectionery Relação com a confeitaria
 * @property-read \Illuminate\Database\Eloquent\Collection|ProductImage[] $images Coleção de imagens do produto
 */
class Product extends Model
{
    /**
     * Atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'confectionery_id'
    ];

    /**
     * Conversões de tipo para atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2'
    ];

    /**
     * Define a relação com a confeitaria à qual este produto pertence.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function confectionery(): BelongsTo
    {
        return $this->belongsTo(Confectionery::class);
    }

    /**
     * Define a relação com as imagens associadas a este produto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
    
    /**
     * Acessor para formatar o preço do produto como moeda (R$).
     *
     * @return string
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }
}