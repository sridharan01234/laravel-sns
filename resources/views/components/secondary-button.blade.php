<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn btn btn btn-secondary']) }}>
    {{ $slot }}
</button>