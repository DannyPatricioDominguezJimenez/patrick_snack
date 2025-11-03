<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // 1. Eliminar la columna 'etiqueta' existente (texto libre)
            $table->dropColumn('etiqueta'); 

            // 2. Agregar la nueva clave foránea para la relación
            $table->foreignId('client_category_id')
                  ->nullable() // Puede ser nula si un cliente no tiene categoría
                  ->after('direccion') // Posición en la tabla
                  ->constrained('client_categories') // Relación con la tabla 'client_categories'
                  ->onDelete('set null'); // Si se elimina una categoría, establece el campo a null
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Revertir: eliminar la clave foránea
            $table->dropConstrainedForeignId('client_category_id');
            
            // Revertir: restaurar la columna 'etiqueta' si fuera necesario (aunque se recomienda dejarla eliminada)
            $table->string('etiqueta', 50)->nullable()->after('direccion');
        });
    }
};