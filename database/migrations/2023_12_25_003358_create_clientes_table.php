<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('rut')->unique();
            $table->string('razon_social');
            $table->string('giro');
            $table->string('direccion');
            $table->string('email_dte');
            $table->smallInteger('tipo_pago')->default(2);
            $table->smallInteger('dias_credito')->default(30);
            $table->string('telefono')->nullable();
            $table->string('email')->nullable();
            $table->string('web')->nullable();
            $table->timestamps();

            $table->foreignIdFor(\App\Comuna::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};
