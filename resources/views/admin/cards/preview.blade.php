<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Preview — {{ $card->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif; }
        .card-canvas { position: relative; overflow: hidden; flex-shrink: 0; }
    </style>
</head>
<body>
@php
    $canvas = $config['canvas'] ?? [];
    $bg = $canvas['background'] ?? ['type' => 'color', 'value' => '#003189'];
    $width = $canvas['width'] ?? 390;
    $height = $canvas['height'] ?? 844;

    $bgStyle = match($bg['type'] ?? 'color') {
        'gradient' => 'background: linear-gradient(to bottom right, ' . ($bg['gradient']['from'] ?? '#003189') . ', ' . ($bg['gradient']['to'] ?? '#0047c8') . ');',
        'image'    => "background-image: url('" . route('admin.media.show', $bg['media_id'] ?? 0) . "'); background-size: cover; background-position: center;",
        default    => 'background-color: ' . ($bg['value'] ?? '#003189') . ';',
    };

    $elements = collect($config['elements'] ?? [])->sortBy('z_index');
@endphp

<div class="card-canvas" style="width: {{ $width }}px; height: {{ $height }}px; {{ $bgStyle }}">
    @foreach($elements as $el)
        @if(!($el['hidden'] ?? false))
        <div style="
            position: absolute;
            left: {{ $el['x'] ?? 0 }}px;
            top: {{ $el['y'] ?? 0 }}px;
            width: {{ $el['width'] ?? 100 }}px;
            height: {{ $el['height'] ?? 30 }}px;
            transform: rotate({{ $el['rotation'] ?? 0 }}deg);
            opacity: {{ $el['opacity'] ?? 1 }};
            z-index: {{ $el['z_index'] ?? 1 }};
            font-family: {{ $el['style']['font_family'] ?? "'Plus Jakarta Sans', sans-serif" }};
            font-size: {{ $el['style']['font_size'] ?? 16 }}px;
            font-weight: {{ $el['style']['font_weight'] ?? '400' }};
            color: {{ $el['style']['color'] ?? '#111827' }};
            text-align: {{ $el['style']['text_align'] ?? 'left' }};
            background-color: {{ $el['style']['background_color'] ?? 'transparent' }};
            border-radius: {{ $el['style']['border_radius'] ?? 0 }}px;
            overflow: hidden;
        ">
            @php $type = $el['type'] ?? 'text'; @endphp
            @if(in_array($type, ['text','name','job_title','department','email','phone','linkedin','calendly','image','logo','banner','photo','qr','button','separator','footer','identity_block']))
                @include('public.card-elements._' . $type, ['el' => $el, 'employee' => $employee])
            @endif
        </div>
        @endif
    @endforeach
</div>

</body>
</html>
