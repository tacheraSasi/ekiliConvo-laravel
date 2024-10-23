<div class="mt-2 p-2" id="explore">
  @if($insights->count())
      <!-- Responsive Grid: 1 column on small, 2 columns on medium, 3 columns on large screens -->
      <div class="mx-auto grid max-w-2xl grid-cols-1 gap-x-2 gap-y-4 sm:grid-cols-2 lg:grid-cols-3 lg:max-w-none">
          @foreach($insights as $insight)
              <x-card>
                  <!-- Article with Fixed Height and Reduced Gaps -->
                  <article class="flex h-[400px] w-full flex-col justify-between bg-white dark:bg-neutral-900   rounded-lg">
                      <!-- Time and Category Badge -->
                      <div class="flex items-center gap-x-2 text-xs">
                          <time datetime="{{ $insight->created_at }}" class="text-neutral-500 mx-2">
                              {{ $insight->created_at->diffForHumans() }}
                          </time>
                          <a href="/category/{{ $insight->category->slug }}" class="relative z-10 rounded-full mx-2 bg-neutral-800 px-2 py-1.5 font-medium text-neutral-400 hover:bg-neutral-700">
                              {{ $insight->category->name }}
                          </a>
                      </div>

                      <!-- Title and Excerpt -->
                      <div class="group relative">
                          <h3 class="mt-3 text-lg font-semibold leading-6 text-neutral-900 dark:text-neutral-300">
                              <a href="{{ route('insights.show', $insight->slug) }}">
                                  <span class="absolute inset-0"></span>
                                  {{ $insight->title }}
                              </a>
                          </h3>
                          <div class="mt-3 line-clamp-3 h-[250px] overflow-hidden text-sm leading-6 text-gray-600 dark:text-neutral-400">
                              {!! Str::limit($insight->content, 300) !!}
                          </div>
                      </div>

                      <!-- Author Info -->
                      <div class="relative mt-6 flex items-center gap-x-2">
                          {{-- <img src="https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                              alt="" class="h-10 w-10 rounded-full bg-neutral-50"> --}}
                          <div class="text-sm leading-6">
                              <p class="font-semibold text-neutral-500">
                                  <a href="/{{ $insight->user->name }}">
                                      <span class="absolute inset-0"></span>
                                      {{ $insight->user->name }}
                                  </a>
                              </p>
                              <p class="text-neutral-600">{{ $insight->likes->count() }} Likes</p>
                          </div>
                      </div>
                  </article>
              </x-card>
          @endforeach
      </div>

      <!-- Pagination Links -->
      <div class="m-4">
          {{ $insights->links() }}
      </div>
  @else
      <x-card>No insights available.</x-card>
  @endif
</div>
