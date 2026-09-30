@if(!empty($employee->email))
<a href="mailto:{{ $employee->email }}" style="text-decoration: none; color: inherit; display: flex; align-items: center; gap: 6px; overflow: hidden;">
    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink:0"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
    <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $employee->email }}</span>
</a>
@endif
