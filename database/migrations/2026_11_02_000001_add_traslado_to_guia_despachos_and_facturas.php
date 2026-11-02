<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Res. Ex. SII N° 144/2026: datos de traslado en guías (52) y facturas (33).
class AddTrasladoToGuiaDespachosAndFacturas extends Migration
{
    private function columnas(Blueprint $table, bool $guia): void
    {
        $table->string('patente', 8)->nullable();
        $table->string('patente_carro', 8)->nullable();
        $table->string('rut_transportista', 12)->nullable();
        $table->string('rut_chofer', 12)->nullable();
        $table->string('nombre_chofer', 30)->nullable();
        $table->string('direccion_destino', 70)->nullable();
        $table->unsignedBigInteger('comuna_destino_id')->nullable();
        $table->date('fecha_salida')->nullable();
        $table->string('hora_salida', 8)->nullable();
        $table->date('fecha_llegada')->nullable();
    }

    public function up()
    {
        Schema::table('guia_despachos', function (Blueprint $table) {
            $this->columnas($table, true);
        });
        Schema::table('facturas', function (Blueprint $table) {
            $this->columnas($table, false);
        });
        if (Schema::hasTable('referencia_borradors') && ! Schema::hasColumn('referencia_borradors', 'rut_otro')) {
            Schema::table('referencia_borradors', function (Blueprint $table) {
                $table->string('rut_otro', 12)->nullable();
            });
        }
    }

    public function down()
    {
        $cols = ['patente', 'patente_carro', 'rut_transportista', 'rut_chofer', 'nombre_chofer',
            'direccion_destino', 'comuna_destino_id', 'fecha_salida', 'hora_salida', 'fecha_llegada'];
        Schema::table('guia_despachos', function (Blueprint $table) use ($cols) {
            $table->dropColumn($cols);
        });
        Schema::table('facturas', function (Blueprint $table) use ($cols) {
            $table->dropColumn($cols);
        });
        if (Schema::hasColumn('referencia_borradors', 'rut_otro')) {
            Schema::table('referencia_borradors', function (Blueprint $table) {
                $table->dropColumn('rut_otro');
            });
        }
    }
}
