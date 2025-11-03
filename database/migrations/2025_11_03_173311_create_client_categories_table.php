<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('client_categories', function (Blueprint $table) {
            $table->id();
            // Nombre de la categoría (ej: "VIP", "Mayorista")
            $table->string('name')->unique(); 
            // Color o código (opcional, para usar en la vista, ej: "#FFC107")
            $table->string('color_code')->nullable(); 
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('client_categories');
    }
};