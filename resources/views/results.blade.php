@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <h2>
            @if (request()->subject)
                Results for &ldquo;{{ request()->subject }}&rdquo;
            @else
                Advanced search results
            @endif
        </h2>
        @include('_manuscripts', ['manuscripts' => $manuscripts])
        <div class="py-6">Found {{ count($manuscripts) }} manuscripts</div>
    </div>
@endsection
