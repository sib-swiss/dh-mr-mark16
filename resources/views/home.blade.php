@extends('layouts.app')

@section('content')
    <div class="container overflow-auto mx-4">
        <h2>{{ $manuscripts->count() }} manuscripts available</h2>
        @include('_manuscripts', ['manuscripts' => $manuscripts])
    </div>
@endsection
