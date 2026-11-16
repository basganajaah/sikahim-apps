@props([
    'eyebrow' => 'SIKAHIM',
    'title',
    'description',
    'action' => null,
    'actionText' => null,
])

<section class="sikahim-hero mb-4 p-4 p-md-5"
    style="background-color: #102a43; background-image: linear-gradient(135deg, #102a43 0%, #0f766e 100%);">
    <div class="sikahim-hero-content row align-items-center">
        <div class="col-lg-8">
            <div class="sikahim-kicker mb-2" style="color: #f5b942;">{{ $eyebrow }}</div>
            <h1 class="display-5 font-weight-bold mb-3 text-white" style="color: #ffffff;">{{ $title }}</h1>
            <p class="lead mb-0" style="color: #eaf5f3;">{{ $description }}</p>
        </div>
        @if ($action && $actionText)
            <div class="col-lg-4 mt-4 mt-lg-0 text-lg-right">
                <a class="btn btn-light btn-lg px-4" href="{{ $action }}">
                    {{ $actionText }} <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        @endif
    </div>
</section>
