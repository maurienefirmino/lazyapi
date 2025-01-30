<table>
    <thead>
        <tr>
            @foreach ($fields as $key => $field)
                <th>
                    {{ $key }}
                </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr>
                @foreach ($fields as $key => $field)
                    <td>
                        {{ $d->$field }}
                    </td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>