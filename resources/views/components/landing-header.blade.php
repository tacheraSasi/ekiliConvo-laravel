<header class="w-full p-6 flex justify-between ">
     <!-- Logo -->
     <div class=" flex items-center">
        <a href="{{ route('home') }}" class="flex gap-2 items-center m-auto m dark:text-neutral-200">
            <x-application-logo class="block h-8 w-8 fill-current" />
            <h1 class="font-bold text-3xl">ekiliConvo</h1>
        </a>
    </div>

    <div class="flex gap-2 items-center">
        @guest
        <a href="{{route('login')}}" class="dark:text-neutral-300 font-bold">Login</a>
        <x-link-button to="{{route('register')}}">{{__('Register')}}</x-link-button>
        @endguest
    </div>

</header>