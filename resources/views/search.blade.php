@extends('layouts.app')

@section('content')
    <div class="w-1/2 mx-auto">
        <h1>Advanced Search</h1>
        <form id="form-search" action="{{ route('results') }}" method="get">
            <div>
                <div class="row">
                    <label for="keywords">Keyword</label>
                    <input type="text" name="keywords" value="" placeholder="e.g. Mark 16">
                </div>
                <div class="row">
                    <label for="title">Name</label>
                    <input type="text" name="title" value="" placeholder="e.g. GA 03">
                </div>
                <div class="row">
                    <label for="shelfmark">Shelfmark</label>
                    <input type="text" name="shelfmark" value="" placeholder="e.g. Vat. gr. 1209">
                </div>
                <div class="row">
                    <label for="docId">NTVRTM Doc ID</label>
                    <input type="text" name="docId" value="" placeholder="e.g. 20003">
                </div>
                <div class="row">
                    <label for="language">Language</label>
                    <select name="language">
                        <option value="">e.g. {{ $languages->first()['name'] }}</option>
                        @foreach ($languages as $langCode => $language)
                            <option value="{{ $language['name'] }}">{{ $language['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button type="submit">Search</button>
        </form>
    </div>
@endsection
