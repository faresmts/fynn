@props(['active'])

@php
$baseClasses = 'inline-flex px-3 rounded rounded-2xl pt-1 transition duration-150 ease-in-out ';
$classes = ($active ?? false)
            ? "bg-zinc-600 text-sm font-medium text-white dark:text-gray-100 "

           : 'text-zinc-600';
@endphp

<a {{ $attributes->merge(['class' => $baseClasses . $classes]) }}>
    {{ $slot }}
</a>
