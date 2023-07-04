<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @hasSection('title')
        <title>@yield('title') - {{ config('app.name') }}</title>
    @else
        <title>{{ config('app.name') }}</title>
    @endif

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url(asset('favicon.ico')) }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    {{-- @livewireStyles
        @livewireScripts --}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    @yield('body')


    <footer class="bg-[#4a74ac] mt-8 py-4">
        <div class="container mx-auto text-center">
            <div class="flex">
                <div class="w-1/4">
                    <a href="https://ntvmr.uni-muenster.de/home" class="navlink" target="_blank">
                        <img class="h-10 inline" src="{{ Vite::asset('resources/images/INTF_Logo_cmyk.jpg') }}"
                            alt="INTF">
                    </a>
                </div>
                <div class="w-1/4">
                    <a href="http://www.snf.ch/" class="navlink" target="_blank">
                        <img class="h-10 inline" src="{{ Vite::asset('resources/images/logo-SNF.png') }}" alt="SNF">
                    </a>
                </div>
                <div class="w-1/4">
                    <a href="https://github.com/sib-swiss/dh-mr-mark16" class="navlink" target="_blank">
                        <img class="h-10 inline" src="{{ Vite::asset('resources/images/logo-GitHub-Mark-64px.png') }}"
                            alt="github">
                    </a>
                </div>
                <div class="w-1/4">
                    <a href="https://sib.swiss" class="navlink" target="_blank">
                        <img class="h-12 inline" src="{{ Vite::asset('resources/images/sib_logo_trans_background.png') }}"
                            alt="SIB">
                    </a>
                </div>
            </div>
            <div class=" mt-4">

                <a href="https://www.php.net/" class="navlink" target="_blank">
                    <img class="inline" src="{{ Vite::asset('resources/images/php-power-micro2.png') }}" alt="Powered by PHP">
                    <!-- <img src="resources/frontend/img/php-power-white.png" alt="Powered by PHP"> -->
                </a>
                -
                <a class="text-white" href="/terms" class="text-light"><small>Terms of use</small></a>

            </div>
        </div>
    </footer>
</body>

</html>
