<?php
// App/Models/Client.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'cedula',
        'nombre',
        'email',
        'telefono',
        'direccion',
        'client_category_id', // ¡Actualizado!
        'fecha_registro',
    ];

    // Un cliente pertenece a una sola categoría
    public function category()
    {
        // El nombre de la función es 'category' y no 'clientCategory'
        // para facilitar el acceso en el código (ej: $cliente->category->name)
        return $this->belongsTo(ClientCategory::class, 'client_category_id');
    }
    
    // ... otros métodos

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}