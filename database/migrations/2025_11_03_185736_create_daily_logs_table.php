<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daily_logs', function (Blueprint $table) {
            $table->id();
            
            // La fecha de la actividad (debe ser única por día)
            $table->date('activity_date')->unique(); 
            
            // El párrafo descriptivo (usamos text para texto largo)
            $table->text('description'); 

            // Si tienes autenticación, es bueno saber quién lo escribió
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); 

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('daily_logs');
    }
};