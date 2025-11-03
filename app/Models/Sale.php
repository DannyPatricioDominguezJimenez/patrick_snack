<?php
// App/Models/Sale.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'sale_date',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'sale_date' => 'date',
    ];

    // Relación 1: Una venta pertenece a un cliente
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // Relación 2: Una venta tiene muchos detalles (productos vendidos)
    public function details()
    {
        // Usamos hasMany para la relación Maestro-Detalle
        return $this->hasMany(SaleDetail::class);
    }
}