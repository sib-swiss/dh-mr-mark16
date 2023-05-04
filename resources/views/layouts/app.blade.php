@extends('layouts.base')

@section('body')
    <nav
        class="mx-auto block w-full border border-white/80 bg-[#4a74ac] bg-opacity-80 py-2 px-4 text-white shadow-md backdrop-blur-2xl backdrop-saturate-200 lg:px-8 lg:py-4">
        <div>
            <div class="flex items-center justify-between text-white">

                <div class="flex items-center justify-between">
                    <a href="{{ route('home') }}"
                        class="mr-4 block cursor-pointer py-1.5 font-sans text-sm font-normal leading-normal text-inherit antialiased">
                        <img src="{{ Vite::asset('resources/images/logo-manuscript.png') }}" alt="homepage">
                    </a>
                    <ul class="hidden items-center gap-6 lg:flex">
                        <li class="block p-1 font-sans text-sm font-normal leading-normal text-inherit antialiased">
                            <a class="flex items-center" href="{{ route('home') }}">
                                About
                            </a>
                        </li>
                        <li class="block p-1 font-sans text-sm font-normal leading-normal text-inherit antialiased">
                            <a class="flex items-center" href="{{ route('home') }}">
                                Content
                            </a>
                        </li>
                        <li class="block p-1 font-sans text-sm font-normal leading-normal text-inherit antialiased">
                            <a class="flex items-center" href="{{ route('search') }}">
                                Advanced Search
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="hidden lg:inline-block">
                    <form action="{{ route('results') }}" method="get" class="flex">
                        <input type="text" name="subject" class="text-gray-800" placeholder="INTF Liste manuscript name"
                            value="{{ request()->subject }}">
                        <button type="submit">Search</button>
                    </form>
                </div>
                <button
                    class="middle none relative ml-auto h-6 max-h-[40px] w-6 max-w-[40px] rounded-lg text-center font-sans text-xs font-medium uppercase text-white transition-all hover:bg-transparent focus:bg-transparent active:bg-transparent disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none lg:hidden"
                    data-collapse-target="navbar">
                    <span class="absolute top-1/2 left-1/2 -translate-y-1/2 -translate-x-1/2 transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" stroke="currentColor"
                            strokeWidth="2">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </span>
                </button>
            </div>
            <div class="block h-0 w-full basis-full overflow-hidden text-white transition-all duration-300 ease-in lg:hidden"
                data-collapse="navbar">{{-- not format this line otherwise cllapse will not work!! --}}<div class="container mx-auto pb-2">
                    <ul class="mt-2 mb-4 flex flex-col gap-2">
                        <li class="block p-1 font-sans text-sm font-normal leading-normal text-inherit antialiased">
                            <a class="flex items-center" href="{{ route('home') }}">
                                About
                            </a>
                        </li>
                        <li class="block p-1 font-sans text-sm font-normal leading-normal text-inherit antialiased">
                            <a class="flex items-center" href="{{ route('home') }}">
                                Content
                            </a>
                        </li>
                        <li class="block p-1 font-sans text-sm font-normal leading-normal text-inherit antialiased">
                            <a class="flex items-center" href="{{ route('search') }}">
                                Advanced Search
                            </a>
                        </li>
                    </ul>

                    <div class="none">
                        <form action="{{ route('results') }}" method="get" class="flex">
                            <input type="text" name="subject" class="text-gray-800"
                                placeholder="INTF Liste manuscript name" value="{{ request()->subject }}">
                            <button type="submit">Search</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>




    @yield('content')

    @isset($slot)
        {{ $slot }}
    @endisset
@endsection
