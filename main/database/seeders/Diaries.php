<?php

namespace Database\Seeders;

use App\Models\DiarioTurnoRotativo;
use App\Models\Person;
use Illuminate\Database\Seeder;

class Diaries extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->generateDiaries();
    }
    private function generateDiaries()
    {
        $people= Person::get();
        foreach ($people as $person => $value) {
            DiarioTurnoRotativo::create([
                'person_id' => $person->id,
            ]);
        }
    }
}
