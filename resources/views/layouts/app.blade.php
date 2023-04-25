@extends('layouts.base')

@section('body')
    <div class="bg-[#4a74ac] text-white">
        <div class="container mx-auto">
            <nav class="flex items-center justify-between">
                <div class="flex items-center ">
                    <div>
                        <a class="block" href="{{ route('home') }}">
                            <img src="{{ Vite::asset('resources/images/logo-manuscript.png') }}" alt="homepage">
                        </a>
                    </div>
                    <div><a class="block p-10" href="{{ route('home') }}">About</a></div>
                    <div><a class="block p-10" href="{{ route('home') }}">Content</a></div>
                    <div><a class="block p-10" href="{{ route('search') }}">Advanced Search</a></div>

                </div>
                <div>
                    <form action="{{ route('results') }}" method="get" class="flex">
                        <input type="text" name="subject" class="text-gray-800" placeholder="INTF Liste manuscript name"
                            value="{{ request()->subject }}">
                        <button type="submit">Search</button>
                    </form>
                </div>


            </nav>
        </div>
    </div>


    @yield('content')

    @isset($slot)
        {{ $slot }}
    @endisset
@endsection
