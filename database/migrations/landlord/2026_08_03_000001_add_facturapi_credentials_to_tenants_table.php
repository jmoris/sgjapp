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
        Schema::table('tenants', function (Blueprint $table) {
            $table->text('facturapi_token')->nullable()->after('database');
            $table->string('facturapi_tenant_id')->nullable()->after('facturapi_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['facturapi_token', 'facturapi_tenant_id']);
        });
    }
};
