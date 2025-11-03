<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            
            // Relación con Clientes (Quién hizo la compra)
            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
            
            // Datos de la Venta
            $table->date('sale_date'); // Fecha de la transacción
            $table->decimal('total_amount', 10, 2); // Total final de la venta
            
            // Estado de la Venta (ej: Creada, Pagada, Cancelada)
            $table->string('status')->default('Creada'); 

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sales');
    }
};