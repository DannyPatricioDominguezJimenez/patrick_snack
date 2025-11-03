<?php
// App/Models/SaleDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    // Relación 1: Un detalle pertenece a una venta (Sale)
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    // Relación 2: Un detalle pertenece a un producto (Product)
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}