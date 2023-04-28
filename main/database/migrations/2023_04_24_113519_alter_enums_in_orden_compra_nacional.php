<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AlterEnumsInOrdenCompraNacional extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Orden_Compra_Nacional', function (Blueprint $table) {
            DB::statement("ALTER TABLE Orden_Compra_Nacional MODIFY COLUMN Estado ENUM('Pendiente', 'Anulada', 'Recibida') DEFAULT 'Pendiente'");
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
            $table->enum('Estado', ['Pendiente', 'Anulada', 'Aprobada', 'Rechazada'])->nullable()->default('Pendiente')->change();
        });
    }
}
