<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash; // ⬅️ Importamos la fachada Hash

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'enabled', // ⬅️ Campo de control de acceso
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        // 🚨 NO INCLUIMOS 'password' => 'hashed' para evitar el error en versiones antiguas
        'enabled' => 'boolean', // ⬅️ Cast para el campo 'enabled'
    ];
    
    // ----------------------------------------------------
    // 🚨 MUTATOR MANUAL PARA HASHEAR LA CONTRASEÑA 🚨
    // ----------------------------------------------------

    /**
     * Hash the password automatically when it is set (Laravel < 10 fix).
     *
     * @param string $value
     * @return void
     */
    public function setPasswordAttribute($value)
    {
        // Solo hashear si se recibe un valor, y hashear usando Hash::make
        if ($value) {
            $this->attributes['password'] = Hash::make($value);
        }
    }
}