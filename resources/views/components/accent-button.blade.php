<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn btn btn-accent']) }}>
    {{ $slot }}
</button>