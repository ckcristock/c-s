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
        Schema::table('accommodation_hotel', function (Blueprint $table) {
            $table->foreign(['accommodation_id'], 'FK_accommodation_hotel_accommodations')->references(['id'])->on('accommodations');
            $table->foreign(['hotel_id'], 'FK_accommodation_hotel_hotels')->references(['id'])->on('hotels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accommodation_hotel', function (Blueprint $table) {
            $table->dropForeign('FK_accommodation_hotel_accommodations');
            $table->dropForeign('FK_accommodation_hotel_hotels');
        });
    }
};
