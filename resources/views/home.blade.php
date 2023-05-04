@extends('layouts.app')

@section('content')
    <div class="container overflow-auto mx-4">        
        @include('_manuscripts', ['manuscripts' => $manuscripts])
    </div>
@endsection
