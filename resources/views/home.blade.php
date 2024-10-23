<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Insights') }}
        </h2>
    </x-slot>

    @include("insights.insights-list")
</x-app-layout>
