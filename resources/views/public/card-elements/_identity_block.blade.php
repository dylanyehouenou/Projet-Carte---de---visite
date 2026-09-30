<div style="display: flex; flex-direction: column; gap: 2px; overflow: hidden;">
    @if($el['data']['show_name'] ?? true)
    <p style="font-weight: 700; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $employee->first_name }} {{ strtoupper($employee->last_name) }}</p>
    @endif
    @if(($el['data']['show_job_title'] ?? true) && !empty($employee->job_title))
    <p style="opacity: 0.85; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $employee->job_title }}</p>
    @endif
    @if(($el['data']['show_department'] ?? false) && !empty($employee->department))
    <p style="opacity: 0.7; font-size: 0.85em; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $employee->department }}</p>
    @endif
</div>
