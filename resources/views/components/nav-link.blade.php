@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-brand-primary dark:border-brand-primary text-sm font-medium leading-5 text-neutral-900 dark:text-slate-100 focus:outline-none focus:border-brand-secondary transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-neutral-500 dark:text-slate-400 hover:text-brand-secondary dark:hover:text-brand-primary hover:border-brand-primary/50 dark:hover:border-brand-primary/50 focus:outline-none focus:text-brand-secondary dark:focus:text-brand-primary focus:border-brand-primary/50 dark:focus:border-brand-primary/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
