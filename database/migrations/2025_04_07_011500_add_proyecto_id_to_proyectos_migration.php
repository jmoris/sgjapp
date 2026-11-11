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
        Schema::table('proyectos', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('proyecto_id')->after('monto_proyecto')->nullable();
            $table->foreign('proyecto_id')->references('id')->on('proyectos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proyectos', function (Blueprint $table) {
            //
            $table->dropColumn('proyecto_id');
        });
    }
};
