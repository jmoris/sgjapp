<?php

use App\Comuna;
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
        Schema::create('factorings', function (Blueprint $table) {
            $table->id();
            $table->string('rut');
            $table->string('razon_social');
            $table->string('direccion');
            $table->foreignIdFor(Comuna::class, 'comuna_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('nombre_contacto');
            $table->string('email_contacto');
            $table->string('email_cesion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('factorings');
    }
};
