<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'product', 'category_id', 'supplier_id',
        'cost_price', 'quantity', 'expiry_date',
        'image'
    ];

    /**
     * Get the supplier that owns the purchase.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the category that owns the purchase.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the product associated with the purchase.
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product');
    }
}



// In Purchase.php
// public function product()
// {
//     return $this->belongsTo(Product::class);
// }
