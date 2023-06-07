<?php

namespace Database\Seeders;

use App\Models\Responsible;
use Illuminate\Database\Seeder;

class ResponsiblesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $responsibles = [
            ['name' => 'DIRECTOR ADMINISTRATIVO Y FINANCIERO', 'person_id' => 11530],
            ['name' => 'ADMINISTRADOR GENERAL (SOFTWARE)', 'person_id' => 1],
            ['name' => 'RESPONSABLE DE RECURSOS HUMANOS', 'person_id' => 11670],
            // Agrega más responsables según sea necesario
        ];

        foreach ($responsibles as $responsible) {
            Responsible::create($responsible);
        }
    }
}
