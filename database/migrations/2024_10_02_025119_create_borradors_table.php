<?php

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
        Schema::create('borradors', function (Blueprint $table) {
            $table->id();
            $table->integer('tipo_doc');
            $table->bigInteger('externo_id');
            $table->dateTime('fecha_emision');
            $table->bigInteger('proyecto_id');
            $table->string('glosa')->nullable();
            $table->foreignIdFor(\App\User::class)->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borradors');
    }
};
