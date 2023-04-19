<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsInOrdenCompraNacional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Orden_Compra_Nacional', function (Blueprint $table) {
            $table->string('format_code', 100)->nullable()->after('Id_Pre_Compra');
            $table->double('Subtotal', 50, 2)->nullable()->after('Id_Pre_Compra');
            $table->double('Iva', 50, 2)->nullable()->after('Id_Pre_Compra');
            $table->double('Total', 50, 2)->nullable()->after('Id_Pre_Compra');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Orden_Compra_Nacional', function (Blueprint $table) {
            $table->dropColumn('format_code');
            $table->dropColumn('Subtotal');
            $table->dropColumn('Iva');
            $table->dropColumn('Total');
        });
    }
}
