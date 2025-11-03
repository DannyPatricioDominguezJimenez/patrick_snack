<?php
// App/Models/ClientCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'color_code'];

    // Una categoría puede tener muchos clientes
    public function clients()
    {
        return $this->hasMany(Client::class);
    }
}