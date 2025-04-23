<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductImage extends Model
{
    protected $fillable = ['image_path', 'product_id'];
    
    protected $appends = ['url'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    /**
     * Get the URL for the image.
     *
     * @return string
     */
    public function getUrlAttribute()
    {
        // Create a fully qualified URL that works with your Apache configuration
        return asset('storage/' . $this->image_path);
    }
}
