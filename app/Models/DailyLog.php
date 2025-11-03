<?php
// App/Models/DailyLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'activity_date', 
        'description', 
        'user_id'
    ];
    
    // Indica a Laravel que trate activity_date como una fecha
    protected $casts = [
        'activity_date' => 'date',
    ];

    // Relación con el usuario autenticado (si aplica)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}