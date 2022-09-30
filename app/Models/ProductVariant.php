<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $table = 'product_variants';

    protected $fillable = [
        'variant', 'variant_id', 'product_id'
    ];

    /**
     * Get the Variant associated with the ProductVariant
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function Variant()
    {
        return $this->hasOne(Variant::class, 'id');
    }
}
