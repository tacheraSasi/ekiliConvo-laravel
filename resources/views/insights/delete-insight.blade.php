<x-danger-button
    x-data=""
    x-on:click.prevent="$dispatch('open-modal', 'confirm-insight-deletion')"
>{{ __('Delete Insight') }}</x-danger-button>

<x-modal name="confirm-insight-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
    <form method="post" action="{{ route('insights.destroy',$insight->id) }}" class="p-6">
        @csrf
        @method('delete')
        <h2 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">
            {{ __('Are you sure you want to delete this insight 🤷🏻🤷🏿?') }}
        </h2>
        <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
            {{ __('Once this insight is deleted, It can never be restored. ') }}
        </p>
        
        <div class="mt-6 flex justify-end">
            <x-secondary-button x-on:click="$dispatch('close')">
                {{ __('Cancel') }}
            </x-secondary-button>
            <x-danger-button class="ms-3">
                {{ __('Delete Insight') }}
            </x-danger-button>
        </div>
    </form>
</x-modal>