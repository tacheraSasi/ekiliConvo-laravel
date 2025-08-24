<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Insights') }}
        </h2>
    </x-slot>

    @include("insights.insights-list")
</x-app-layout>
