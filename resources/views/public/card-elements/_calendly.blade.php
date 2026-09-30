@if(!empty($employee->calendly_url))
<a href="{{ $employee->calendly_url }}" target="_blank" rel="noopener" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 6px;">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    <span>Prendre rendez-vous</span>
</a>
@endif
