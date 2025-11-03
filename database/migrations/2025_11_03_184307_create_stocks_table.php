<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stocks', function (Blueprint $table) {
            $table->id();
            
            // Relación (Usamos unsignedBigInteger para coincidir con product->id)
            $table->unsignedBigInteger('product_id')->unique(); // UN Producto solo tiene UN registro de stock
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            
            // Campo de Cantidad
            $table->integer('quantity')->default(0); 

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('stocks');
    }
};