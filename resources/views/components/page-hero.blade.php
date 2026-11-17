@props([
    'eyebrow' => null,
    'title',
    'description',
    'action' => null,
    'actionText' => null,
])

<section class="sikahim-hero mb-4">
    <div class="sikahim-hero-content row align-items-end">
        <div class="col-lg-8">
            <h1>{{ $title }}</h1>
            <p>{{ $description }}</p>
        </div>
        @if ($action && $actionText)
            <div class="col-lg-4 mt-4 mt-lg-0 text-lg-right">
                <a class="btn sk-hero-action" href="{{ $action }}">{{ $actionText }}</a>
            </div>
        @endif
    </div>
</section>
