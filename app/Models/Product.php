<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    
    // 1. Campos permitidos para Asignación Masiva
    protected $fillable = [
        'sku', 
        'name', 
        'description', 
        'price', 
        'product_category_id' // Clave foránea
    ];

    // 2. Relación: Un producto pertenece a una Categoría de Producto
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    // 3. Relación: Un producto tiene un registro de Stock
    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }
}