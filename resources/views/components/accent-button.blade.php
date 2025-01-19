<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-accent']) }}>
    {{ $slot }}
</button>