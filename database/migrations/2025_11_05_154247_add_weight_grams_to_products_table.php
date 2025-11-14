<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Añade la nueva columna 'weight_grams' como un entero sin signo (nunca será negativo)
            $table->unsignedInteger('weight_grams')->nullable()->after('price'); 
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Elimina la columna si se revierte la migración
            $table->dropColumn('weight_grams');
        });
    }
};