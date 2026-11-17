@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'sk-status']) }}>
        {{ $status }}
    </div>
@endif
