@props(['status' => \App\IdeaStatus::Pending])

@php
    $classes = 'inline-block rounded-full border px-2 py-1 text-xs font-medium mt-1';

    $classes .= match ($status) {
        \App\IdeaStatus::Pending => ' bg-yellow-500/10 text-yellow-500 border-yellow-500',
        \App\IdeaStatus::InProgress => ' bg-blue-500/10 text-blue-500 border-blue-500',
        \App\IdeaStatus::Completed => ' bg-primary/10 text-primary border-primary',
    };
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $status->label() }}
</span>
