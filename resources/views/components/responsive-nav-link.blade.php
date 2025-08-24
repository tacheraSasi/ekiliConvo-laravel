@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-brand-primary dark:border-brand-primary text-start text-base font-medium text-brand-dark dark:text-brand-primary bg-brand-primary/10 dark:bg-brand-primary/20 focus:outline-none focus:text-brand-dark dark:focus:text-brand-primary focus:bg-brand-primary/20 dark:focus:bg-brand-primary/30 focus:border-brand-secondary dark:focus:border-brand-secondary transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-slate-400 hover:text-gray-800 dark:hover:text-slate-200 hover:bg-gray-50 dark:hover:bg-slate-700 hover:border-brand-primary/50 dark:hover:border-brand-primary/50 focus:outline-none focus:text-gray-800 dark:focus:text-slate-200 focus:bg-gray-50 dark:focus:bg-slate-700 focus:border-brand-primary/50 dark:focus:border-brand-primary/50 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
