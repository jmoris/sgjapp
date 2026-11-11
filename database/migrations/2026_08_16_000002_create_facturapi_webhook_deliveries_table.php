<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('facturapi_webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->uuid('delivery_id')->unique();
            $table->string('event_type', 64)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('facturapi_webhook_deliveries');
    }
};
