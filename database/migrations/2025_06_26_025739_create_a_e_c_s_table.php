<?php

use App\Cesion;
use App\Factura;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('a_e_c_s', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Factura::class, 'factura_id')
                ->constrained()
                ->onDelete('cascade');
            $table->smallInteger('estado'); // 0: Enviado, 1: Aceptado, 2: Rechazado
            $table->string('track_id')->nullable();
            $table->foreignIdFor(Cesion::class, 'cesion_id')
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a_e_c_s');
    }
};
