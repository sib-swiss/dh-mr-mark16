@extends('layouts.app')

@section('content')
    <div class="container mx-auto">

        <h1>
            <a href="javascript:location.reload()">{{ $manuscript->getDisplayname() }}</a>
            <small>
                @if ('CSRPC' === $manuscript->name)
                    Printed edition ©
                @else
                    Images ©
                @endif
            </small>
            </a>


            @foreach ($manuscript->partners as $partner)
                <a href="{{ $partner->url ? $partner->url : '#' }}" style="text-decoration: none;" target="_blank">
                    @if ($partner->getFirstMedia())
                        <img src="{{ $partner->getFirstMediaUrl() }}" alt="{{ $manuscript->getMeta('dcterm-provenance') }}"
                            style="max-width: 150px; max-height: 150px;">
                    @else
                        <img data-src="holder.js/150x160?random=yes&text=Partner">
                    @endif
                </a>
            @endforeach

            <a class="btn btn-sm m-1 px-1 py-0" role="button" target="_blank"
                href="{{ $manuscript->getMeta('dcterm-isVersionOf') }}">
                <div
                    style="font-size: 12px !important; font-weight: 400 !important; color: rgb(74, 116, 172); background-color: rgba(0, 0, 0, 0);">
                    <i class="fas fa-camera fa-2x"></i>
                </div>
            </a>

        </h1>

        <div class="flex">
            <div>
                <p>
                    <dcterms:alternative>{{ $manuscript->getMeta('dcterm-alternative') }}</dcterms:alternative>
                </p>
                <p><span class="show-metadata">Shelfmark: </span>
                    <dcterms:isFormatOf>{{ $manuscript->getMeta('dcterm-isFormatOf') }}</dcterms:isFormatOf>
                </p>
                <p><span class="show-metadata">Date: </span>
                    <dcterms:date xml:lang="en">{{ $manuscript->getMeta('dcterm-date') }}</dcterms:date>
                </p>
                <p><span class="show-metadata">Language: </span>
                    <dcterms:language xml:lang="en">{{ $manuscript->getLangExtended() }}</dcterms:language>
                </p>
            </div>


            <div>
                @if ($manuscript->getMeta('dcterm-coverage'))
                    <p>
                        <dcterms:coverage>{{ $manuscript->getMeta('dcterm-coverage') }}</dcterms:coverage>
                    </p>
                @endif

                <p>
                    <span class="show-metadata">
                        @if ('CSRPC' === $manuscript->name)
                            Transliteration:
                        @else
                            Transcription:
                        @endif
                    </span>

                    @foreach ($manuscript->getMetas('dcterm-creator') as $creator)
                        <dcterms:creator>{{ $creator }}</dcterms:creator>
                        @if (!$loop->last)
                            ,
                        @endif
                    @endforeach
                </p>

                @if ($manuscript->getMeta('dcterm-contributor'))
                    <p>
                        <span class="show-metadata">Encoding: </span>

                        <dcterms:creator>{{ $manuscript->getMeta('dcterm-contributor') }}</dcterms:creator>
                    </p>
                @endif

                <p>
                    <span class="show-metadata">Nakala: </span>
                    <a id="ddb-hybrid" class="btn btn-info btn-sm m-1 px-1 py-0 text-white" role="button" target="_blank"
                        href="{{ str_replace(['api.', 'datas/'], '', $manuscript->url) }}">
                        metadata
                    </a>
                </p>

            </div>


            <div>TODO EXTERNAL LINKS</div>

        </div>


        <div>
            <h2>{{ $manuscript->name }}: Folios</h2>

        </div>

        @foreach ($manuscript->folios as $folio)
            <div>{{ $folio->name }}</div>
            <div>
                @if ($folio->getFirstMedia())
                    <img src="{{ $folio->getFirstMediaUrl() }}">
                @else
                    NO IMG
                @endif
            </div>
        @endforeach

    </div>
@endsection
