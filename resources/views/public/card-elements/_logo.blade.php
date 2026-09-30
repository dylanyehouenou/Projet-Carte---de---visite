@if(!empty($el['data']['media_id']))
<img src="{{ route('admin.media.show', $el['data']['media_id']) }}" alt="logo" style="width:100%; height:100%; object-fit: contain;">
@endif
