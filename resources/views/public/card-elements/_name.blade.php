@php
    $format = $el['data']['format'] ?? 'full';
    $name = match($format) {
        'first'    => $employee->first_name,
        'last'     => strtoupper($employee->last_name),
        'initials' => strtoupper(mb_substr($employee->first_name, 0, 1)) . strtoupper(mb_substr($employee->last_name, 0, 1)),
        default    => $employee->first_name . ' ' . strtoupper($employee->last_name),
    };
@endphp
@if($name)
<p style="overflow-wrap: break-word; white-space: nowrap; overflow: hidden;">{{ $name }}</p>
@endif
