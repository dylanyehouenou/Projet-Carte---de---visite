@if(!empty($employee->slug))
    @if(!empty($employee->photo_path))
    <img src="{{ route('card.photo', $employee->slug) }}" alt="{{ $employee->first_name }}" style="width:100%; height:100%; object-fit:cover; border-radius:inherit;">
    @else
    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background: rgba(255,255,255,0.2); border-radius:inherit; font-size: 2em; font-weight: 700; color: inherit;">
        {{ strtoupper(mb_substr($employee->first_name, 0, 1)) }}{{ strtoupper(mb_substr($employee->last_name, 0, 1)) }}
    </div>
    @endif
@endif
