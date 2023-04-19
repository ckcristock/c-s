<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        DB::raw("INSERT INTO `Comprobante_Consecutivo` VALUES (15, 'Orden de compra', 'OC', 1, NULL, NULL, NULL, 6, 'CO-F-02 VERSIÓN 04', 7, 'Orden_Compra_Nacional', 1, 1, '2023-04-18 17:38:45', '2023-04-18 08:30:15');
        ");
        //eliminar inventario por vencer
        //otro comprobante consecutivo de mafe
        //agregar permisos de orden de compra
        // menu id 9 agregar permiso 2
    }
}
