@extends('layouts.base')

@section('body')
    <div class="bg-blue-600 text-white">
        <div class="container mx-auto">
            <nav class="flex">
                <div><a class="block p-10" href="{{ route('home') }}">HOME</a></div>
                <div><a class="block p-10" href="{{ route('home') }}">About</a></div>
                <div><a class="block p-10" href="{{ route('home') }}">Content</a></div>
                <div><a class="block p-10" href="{{ route('search') }}">Advanced Search</a></div>
            </nav>
        </div>
    </div>


    @yield('content')

    @isset($slot)
        {{ $slot }}
    @endisset
@endsection
