<?php

use App\Models\Alert;
use App\Models\Person;

if (!function_exists('createAlert')) {
    function createAlert($data, $id, $description, $info, $icon, $type)
    {
        $allocator_person = Person::where('id', $data['allocator_person_id'])->fullName()->first();
        Alert::create([
            'user_id' => $data['person_id'],
            'person_id' => $data['allocator_person_id'],
            'type' => $type,
            'description' => $allocator_person->full_names . $description . $info->code,
            'url' => '/manufactura/ingenieria/ver/' . $id,
            'icon' => $icon,
        ]);
        return 'Creado con éxito';
    }
}
