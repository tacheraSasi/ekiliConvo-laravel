<x-app-layout>
    <x-card>
        <div class="container mx-auto">
            <h1 class="text-2xl font-bold">Edit Insight</h1>
        
            <form action="{{ route('insights.update', $insight->id) }}" method="POST">
                @csrf
                @method('PUT')
        
                <div class="mt-2">
                    <x-input-label for="title" :value="__('Title')" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full focus:outline-none focus:border-none focus:ring focus:ring-neutral-800 dark:focus:ring-neutral-400" :value="$insight->title" required autofocus autocomplete="title" />
                    <x-input-error class="mt-2" :messages="$errors->get('title')" />
                </div>
        
                <div class="mt-2">
                    <label class="block font-bold" for="category_id">Category</label>
                    <select name="category_id" id="category_id" class="form-select w-full border-gray-300 dark:border-neutral-800 dark:bg-neutral-900 dark:text-gray-300 focus:outline-none focus:border-none focus:ring focus:ring-neutral-800 dark:focus:ring-neutral-400 rounded-md shadow-sm" required>
                        <option value="{{ $insight->category->id }}">{{ $insight->category->name }}</option>
                        
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
        
                <div class="mt-2">
                    <label class="block font-bold" for="content">Content</label>
                    <x-input-error class="mt-2" :messages="$errors->get('content')" />
                    <textarea name="content" id="content" class="form-textarea w-full p-4 rounded-lg border dark:bg-neutral-900 dark:border-neutral-700 focus:outline-none focus:border-none focus:ring focus:ring-neutral-800 dark:focus:ring-neutral-400 placeholder-neutral-500 dark:placeholder-neutral-400" rows="8" required>{{ $insight->content }}</textarea>
                </div>
        
                <div class="mt-2">
                    <x-primary-button>{{ __('Edit Insight') }}</x-primary-button>                </div>
            </form>
        </div>
    </x-card>
</x-app-layout>