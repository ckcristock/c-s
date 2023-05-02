<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteSeparableInCategoriaNueva extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Categoria_Nueva', function (Blueprint $table) {
            $table->dropColumn('Aplica_Separacion_Categorias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Categoria_Nueva', function (Blueprint $table) {
            $table->enum('Aplica_Separacion_Categorias', ['Si', 'No'])->nullable()->default('No')->after('Compra_Internacional');
        });
    }
}
