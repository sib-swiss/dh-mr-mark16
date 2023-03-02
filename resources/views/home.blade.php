@extends('layouts.app')

@section('content')
    <div class="container mx-auto">
        <h2>{{ $manuscripts->count() }} manuscripts available</h2>
        <table class="border-collapse table-auto w-full text-sm">
            <thead>
                <tr>
                    <th
                        class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        #</th>
                    <th
                        class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        Image</th>
                    <th
                        class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        Details</th>
                    <th
                        class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                        Abstract</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-slate-800">
                @foreach ($manuscripts as $manuscript)
                    <tr>
                        <td
                            class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                            {{ $manuscript->getMeta('dcterm-temporal') }}</td>
                        <td
                            class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                            IMAGE</td>
                        <td
                            class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                            <a href="{{ route('manuscript.show', $manuscript) }}">{{ $manuscript->name }}</a>

                            {{-- 
                            <a href="{{ $request_uri . 'show?id=' . $manuscript->getEncodedId() }}" style="font-family: 'Gentium Plus'; font-weight: bold;">
                                <dcterms:bibliographiccitation>{{ $manuscript->getDisplayname() }}</dcterms:bibliographiccitation>
                            </a>
                            <p>
                                <span><dcterms:isformatof>{{ $manuscript->getMeta('dcterm-isFormatOf') }}</dcterms:isformatof></span><br>
                                <span><dcterms:language xml:lang="en">{{ $manuscript->getLangExtended() }}</dcterms:language></span><br>
                                <span><dcterms:date xml:lang="en">{{ $manuscript->getMeta('dcterm-date') }}</dcterms:date></span><br>
                            </p>
                         --}}
                        </td>
                        <td
                            class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                            {{ $manuscript->getMeta('dcterm-abstract') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
