<table>
    <thead>
        <tr class="title">
            <th>NOMBRE</th>
            <th>IDENTIFICACIÓN</th>
            <th>DEPENDENCIA</th>
            <th>CARGO</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($people as $key => $person)
            <tr>
                <td>
                    {{ strtoupper($person->first_name) }}
                    {{ strtoupper($person->second_name) }}
                    {{ strtoupper($person->first_surname) }}
                    {{ strtoupper($person->second_surname) }}
                </td>
                <td>{{ $person->identifier }}</td>
                <td>{{ optional(optional(optional($person->contractultimate)->position)->dependency)->name }}</td>
                <td>{{ optional(optional($person->contractultimate)->position)->name }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
