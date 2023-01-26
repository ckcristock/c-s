<?php

use App\Models\Alert;
use App\Models\Person;

if (!function_exists('createAlert')) {
    function createAlert($data, $id, $description, $info = null)
    {
        $allocator_person = Person::where('id', $data['allocator_person_id'])->fullName()->first();
        Alert::create([
            'user_id' => $data['person_id'],
            'person_id' => $data['allocator_person_id'],
            'type' => 'Orden de producción (ingeniería)',
            'description' => $allocator_person->full_names . $description . $info->code,
            'url' => '/manufactura/ingenieria/ver/' . $id,
            'icon' => 'fas fa-tools',
        ]);
        return 'Creado con éxito';
    }
}
