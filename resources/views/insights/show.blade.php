<x-app-layout :insight="$insight">
    {{-- 
    @section('meta_title', $insight->title)
    @section('meta_description', Str::limit($insight->content, 150)) 
    --}}

    <div class="container mx-auto mt-10 p-4">
        <!-- Title and Category Information -->
        <h1 class="font-extrabold text-neutral-900 dark:text-neutral-200 leading-tight text-4xl">{{ $insight->title }}</h1>
        <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
            Category: 
            <span class="font-semibold text-neutral-600 dark:text-neutral-300">
                {{ $insight->category->name }}
            </span> 
            
        </p>

        <!-- Insight Content -->
        <x-card >
            <div class="flex items-center justify-between">
                
                <div class="flex-items-center gap-x-2 text-xs">
                    <a href="/category/{{ $insight->category->name }}" class="relative z-10 rounded-full mx-2 bg-neutral-800 px-2 py-1.5 font-medium text-neutral-400 hover:bg-neutral-700">
                        {{ $insight->user->name }}
                    </a>
    
                    <time datetime="{{ $insight->created_at }}" class="text-neutral-500 mx-2">
                        {{ $insight->created_at->diffForHumans() }}
                    </time>
                </div>

                <div class="flex items-center gap-x-2">
                    @if (Auth::user() == $insight->user)
                        <x-secondary-link :to="route('insights.edit',$insight->id)">
                            {{__('Edit')}} 
                        </x-secondary-link>
                    @endif
                    <x-primary-button
                        x-data="" 
                        x-on:click.prevent="$dispatch('open-modal', 'share-insight')">
                        {{__('Share')}} 
                    </x-primary-button>
                </div>
                {{-- share modal --}}
                <x-modal name="share-insight">
                    <div class="p-4">
                        <h1 class="text-2xl font-bold">Share insight</h1> <br>
                        <p>
                            <b>Copy link:</b>  
                            <span id="insight-link">{{request()->url()}}</span>
                        </p>
                        <div class="mt-6 flex justify-end gap-2">
                            <x-primary-button  id="link-copy-button">
                                {{ __('Copy') }}
                            </x-primary-button>

                            <x-secondary-button x-on:click="$dispatch('close')">
                                {{ __('Cancel') }}
                            </x-secondary-button>
            
                            
                        </div>
                    </div>
                </x-modal>
            </div>
            <article class=" max-w-none" id="content-output">
                
                <md-block class="">
                    <span class="font-inherit w-full">
                        {!! $insight->html !!}
                    </span>
                </md-block>

                @if (Auth::user() == $insight->user)
                    <div class="flex mt-4">
                        @include("insights.delete-insight")
                    </div>
                @endif
                
            </article>
            
        </x-card>

        <!-- Like Button with Enhanced Styling -->
        <div class="mt-6 flex items-center space-x-4 dark:text-neutral-200">
            <form action="{{ route('insights.like', $insight->id) }}" method="POST" class="flex items-center">
                @csrf
                <button type="submit" class="flex items-center text-customGreenDark dark:text-customGreenLight hover:text-green-300 dark:hover:text-green-300 transition duration-300">
                    <svg class="w-6 h-6 inline mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 6.5 3.5 5 5.5 5c1.54 0 3.04.99 3.57 2.36h1.87C13.46 5.99 14.96 5 16.5 5 18.5 5 20 6.5 20 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path>
                    </svg>
                    <span class="font-semibold text-lg">{{ $insight->likes->count() }} Likes</span>
                </button>
            </form>
        </div>

        <!-- Comments Section -->
        <div class="mt-10">
            <h2 class="text-3xl font-semibold text-neutral-800 dark:text-neutral-200">Comments ({{ $insight->comments->count() }})</h2>

            <!-- Comment List -->
            <div class="mt-6 space-y-6">
                @foreach($insight->comments as $comment)
                    <div class="p-4 rounded-lg bg-neutral-50 dark:bg-neutral-800 shadow-md">
                        <p class="text-neutral-700 dark:text-neutral-300">{{ $comment->comment }}</p>
                        <p class="mt-2 text-sm text-neutral-500 dark:text-neutral-400">
                            {{ $comment->user->name }} | {{ $comment->created_at->diffForHumans() }}
                        </p>
                    </div>
                @endforeach
            </div>

            <!-- Add Comment Form -->
            @auth
                <form action="{{ route('comments.store', $insight->id) }}" method="POST" class="mt-6">
                    @csrf
                    <div>
                        <textarea name="comment" rows="4" class="w-full p-4 rounded-lg border dark:bg-neutral-900 text-black dark:text-white dark:border-neutral-700 focus:outline-none focus:ring focus:ring-neutral-500 dark:focus:ring-neutral-400 placeholder-neutral-500 dark:placeholder-neutral-400" placeholder="Write a comment..." required></textarea>
                    </div>
                    <div class="mt-4">
                        <x-primary-button type="submit" class="bg-customGreenDark dark:bg-customGreenLight text-white px-5 py-2 rounded-md border-none  hover:bg-neutral-700 dark:hover:bg-neutral-400 transition duration-300">
                            Add Comment
                        </x-primary-button>
                    </div>
                </form>
            @else
                <p class="text-neutral-600 dark:text-neutral-400 mt-4">You must be logged in to comment.</p>
            @endauth
        </div>
    </div>
</x-app-layout>
