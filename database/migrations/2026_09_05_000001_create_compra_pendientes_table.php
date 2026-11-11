<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabla local de documentos de compra que el SII tiene como PENDIENTE de acuse de recibo.
     * La sincronización la va poblando de forma acumulativa (mes actual + anterior), así que la
     * pantalla de "Pendientes" siempre muestra la lista completa aunque una llamada al RCV del
     * SII falle en una corrida puntual. Al registrar un evento de acuse/reclamo la fila se
     * elimina; la sincronización también poda las que el SII ya no reporta como pendientes.
     *
     * No confundir con `documento_pendientes` (App\DocumentoPendiente), que trackea el estado
     * SII por track_id de documentos EMITIDOS (ventas).
     */
    public function up(): void
    {
        Schema::create('compra_pendientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('tipo_doc');
            $table->string('rut_emisor');
            $table->string('razon_social')->nullable();
            $table->unsignedBigInteger('folio');
            $table->date('fecha_emision')->nullable();
            $table->unsignedBigInteger('monto_total')->default(0);
            $table->string('periodo', 6)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['tipo_doc', 'rut_emisor', 'folio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_pendientes');
    }
};
