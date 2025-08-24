@props(["to"])

<a href="{{$to}}" {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-brand-secondary dark:bg-brand-primary border border-transparent rounded-md font-semibold text-xs text-white dark:text-brand-darker uppercase tracking-widest hover:bg-brand-dark dark:hover:bg-brand-light hover:text-white dark:hover:text-brand-darker focus:bg-brand-dark dark:focus:bg-brand-light active:bg-brand-darker dark:active:bg-brand-accent focus:outline-none focus:ring-2 focus:ring-brand-accent focus:ring-offset-2 dark:focus:ring-offset-brand-darker transition ease-in-out duration-150']) }}>
    {{ $slot }}
</a>
