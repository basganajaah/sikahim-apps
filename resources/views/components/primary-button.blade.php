<button {{ $attributes->merge(['type' => 'submit', 'class' => 'sk-btn sk-btn-primary']) }}>
    {{ $slot }}
</button>
