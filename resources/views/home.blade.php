@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <h2>{{ $manuscripts->count() }} manuscripts available</h2>
        @include('_manuscripts', ['manuscripts' => $manuscripts])
    </div>
@endsection
