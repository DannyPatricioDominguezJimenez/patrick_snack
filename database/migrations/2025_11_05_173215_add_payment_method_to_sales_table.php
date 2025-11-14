<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Añade la nueva columna. Usamos 'default' por si hay ventas antiguas.
            $table->string('payment_method')->default('Efectivo')->after('total_amount');
            
            // Opcional: Si quieres cambiar el tipo de columna 'status', aunque string funciona
            // $table->enum('status', ['Pagada', 'Pendiente'])->default('Pagada')->change();
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });
    }
};
