@php
    $action = $el['data']['action'] ?? 'url';
    $label  = $el['data']['label'] ?? 'Bouton';
    $href = match($action) {
        'email'    => 'mailto:' . ($employee->email ?? ''),
        'phone'    => 'tel:' . preg_replace('/\s+/', '', $employee->phone ?? ''),
        'linkedin' => $employee->linkedin_url ?? '#',
        'calendly' => $employee->calendly_url ?? '#',
        'vcard'    => !empty($employee->slug) ? route('card.vcard', $employee->slug) : '#',
        default    => $el['data']['url'] ?? '#',
    };
@endphp
@if($href && $href !== '#' && $href !== 'mailto:' && $href !== 'tel:')
<a href="{{ $href }}" target="{{ $action === 'url' ? '_blank' : '_self' }}" rel="noopener" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%; text-decoration: none; color: inherit; font-weight: inherit; font-size: inherit;">
    {{ $label }}
</a>
@else
<span style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;">{{ $label }}</span>
@endif
