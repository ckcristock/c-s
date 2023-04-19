<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableInThirdParties extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('third_parties', function (Blueprint $table) {
            $table->dropColumn('third_party_type');
            $table->boolean('is_client')->nullable();
            $table->boolean('is_supplier')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('third_parties', function (Blueprint $table) {
            $table->dropColumn('is_client');
            $table->dropColumn('is_supplier');
            $table->enum('third_party_type', ['Cliente', 'Proveedor'])->nullable();
        });
    }
}
