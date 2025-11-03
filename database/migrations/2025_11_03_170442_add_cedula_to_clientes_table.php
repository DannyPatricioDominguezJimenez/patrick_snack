<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // CAMBIAR 'clientes' por 'clients'
        Schema::table('clients', function (Blueprint $table) {
            $table->string('cedula', 10)->nullable()->after('id'); 
        });
    }

    public function down()
    {
        // CAMBIAR 'clientes' por 'clients'
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('cedula');
        });
    }
};