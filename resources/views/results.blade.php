@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <h2>Advanced search results</h2>
        @include('_manuscripts', ['manuscripts' => $manuscripts])
        <div class="py-6">Found {{ count($manuscripts) }} manuscripts</div>
    </div>
@endsection
