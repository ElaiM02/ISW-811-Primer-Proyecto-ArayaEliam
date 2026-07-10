@props(['status' => \App\Enums\IdeaStatus::Pending])

@php
    $classes = 'inline-block rounded-full text-xs font-medium px-2 border';

    $classes .= match ($status) {
        \App\Enums\IdeaStatus::Pending    => ' bg-yellow-500/10 text-yellow-500 border-yellow-500',
        \App\Enums\IdeaStatus::InProgress => ' bg-blue-500/10 text-blue-500 border-blue-500',
        \App\Enums\IdeaStatus::Completed  => ' bg-primary/10 text-primary border-primary',
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $status->label() }}
</span>