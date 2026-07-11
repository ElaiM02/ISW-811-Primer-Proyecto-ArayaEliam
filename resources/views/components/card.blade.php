@props(['is' => 'a'])

<{{ $is }} {{ $attributes->merge(['class' => 'block border rounded-lg bg-card p-4 text-sm']) }}>
    {{ $slot }}
</{{ $is }}>