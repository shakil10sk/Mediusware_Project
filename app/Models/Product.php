<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title', 'sku', 'description'
    ];

    /**
     * Get all of the ProductVariantPrice for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ProductVariantPrice()
    {
        return $this->hasMany(ProductVariantPrice::class, 'product_id');
    }

    /**
     * Get all of the ProductVariant for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ProductVariant()
    {
        return $this->hasMany(ProductVariant::class, 'product_id');
    }

    /**
     * Get all of the ProductImage for the Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ProductImage()
    {
        return $this->hasMany(ProductImage::class, 'product_id');
    }
}
