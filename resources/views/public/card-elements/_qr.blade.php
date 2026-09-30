@if(!empty($employee->slug))
<img src="{{ route('card.qr', $employee->slug) }}" alt="QR Code" style="width:100%; height:100%; object-fit:contain;">
@endif
