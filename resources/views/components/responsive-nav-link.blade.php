@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2.5 border-l-4 border-marigold text-start text-xs font-mono font-bold uppercase tracking-wider text-plum-ink bg-marigold/10 focus:outline-none transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2.5 border-l-4 border-transparent text-start text-xs font-mono font-bold uppercase tracking-wider text-plum-ink/70 hover:text-plum-ink hover:bg-petal-cream/40 focus:outline-none transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
