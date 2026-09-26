@props([
    'name',
    'class' => '',
])

<i {{ $attributes->merge(['class' => "ti ti-{$name} {$class} leading-none inline-flex items-center justify-center"]) }}></i>
