<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Comprobante_Consecutivo', function (Blueprint $table) {
            $table->integer('Id_Comprobante_Consecutivo', true);
            $table->string('Tipo', 45)->nullable();
            $table->string('Prefijo', 3)->nullable();
            $table->tinyInteger('Anio')->default(0);
            $table->tinyInteger('Mes')->nullable();
            $table->tinyInteger('Dia')->nullable();
            $table->tinyInteger('city')->nullable();
            $table->integer('longitud')->nullable();
            $table->string('format_code', 50)->nullable();
            $table->integer('Consecutivo')->nullable();
            $table->string('table_name', 50)->nullable();
            $table->integer('company_id')->nullable()->default(1);
            $table->tinyInteger('editable')->nullable()->default(1);
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comprobante_consecutivo');
    }
};
