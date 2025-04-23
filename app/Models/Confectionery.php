<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo de Confeitaria
 * 
 * Representa as confeitarias cadastradas no sistema, com seus dados básicos,
 * endereço e relacionamento com produtos.
 * 
 * @property int $id
 * @property string $name Nome da confeitaria
 * @property float|null $latitude Latitude para localização no mapa
 * @property float|null $longitude Longitude para localização no mapa
 * @property string $phone Telefone de contato
 * @property int $address_id ID do endereço relacionado
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read Address $address Relação com o endereço
 * @property-read \Illuminate\Database\Eloquent\Collection|Product[] $products Coleção de produtos da confeitaria
 */
class Confectionery extends Model
{   
    /**
     * Atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'phone',
        'address_id',
    ];

    /**
     * Conversões de tipo para atributos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    /**
     * Define a relação com o endereço da confeitaria.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * Define a relação com os produtos oferecidos por esta confeitaria.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
    
    /**
     * Retorna o endereço completo formatado.
     *
     * @return string
     */
    public function getFullAddressAttribute(): string
    {
        if (!$this->address) {
            return 'Endereço não cadastrado';
        }
        
        return sprintf(
            '%s, %s - %s, %s/%s, CEP: %s',
            $this->address->street,
            $this->address->number,
            $this->address->neighborhood,
            $this->address->city,
            $this->address->state,
            $this->address->cep
        );
    }
}
