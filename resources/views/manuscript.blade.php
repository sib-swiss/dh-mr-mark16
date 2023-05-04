@extends('layouts.app')

@section('content')
    <div class="mx-2">

        <div class="flex justify-between">
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
                            <img src="{{ $partner->getFirstMediaUrl() }}" alt="{{ $manuscript->getMeta('provenance') }}"
                                style="max-width: 150px; max-height: 150px;">
                        @else
                            <img data-src="holder.js/150x160?random=yes&text=Partner">
                        @endif
                    </a>
                @endforeach

                <a class="btn btn-sm m-1 px-1 py-0" role="button" target="_blank"
                    href="{{ $manuscript->getMeta('isVersionOf') }}">
                    <div
                        style="font-size: 12px !important; font-weight: 400 !important; color: rgb(74, 116, 172); background-color: rgba(0, 0, 0, 0);">
                        <i class="fas fa-camera fa-2x"></i>
                    </div>
                </a>

            </h1>

            <div class="flex">
                <div>
                    <p>
                        <dcterms:alternative>{{ $manuscript->getMeta('alternative') }}</dcterms:alternative>
                    </p>
                    <p><span class="show-metadata">Shelfmark: </span>
                        <dcterms:isFormatOf>{{ $manuscript->getMeta('isFormatOf') }}</dcterms:isFormatOf>
                    </p>
                    <p><span class="show-metadata">Date: </span>
                        <dcterms:date xml:lang="en">{{ $manuscript->getMeta('date') }}</dcterms:date>
                    </p>
                    <p><span class="show-metadata">Language: </span>
                        <dcterms:language xml:lang="en">{{ $manuscript->getLangExtended() }}</dcterms:language>
                    </p>
                </div>


                <div>
                    @if ($manuscript->getMeta('coverage'))
                        <p>
                            <dcterms:coverage>{{ $manuscript->getMeta('coverage') }}</dcterms:coverage>
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

                    @if ($manuscript->getMeta('contributor'))
                        <p>
                            <span class="show-metadata">Encoding: </span>

                            <dcterms:creator>{{ $manuscript->getMeta('contributor') }}</dcterms:creator>
                        </p>
                    @endif

                    <p>
                        <span class="show-metadata">Nakala: </span>
                        <a id="ddb-hybrid" class="btn btn-info btn-sm m-1 px-1 py-0 text-white" role="button"
                            target="_blank" href="{{ str_replace(['api.', 'datas/'], '', $manuscript->url) }}">
                            metadata
                        </a>
                    </p>

                </div>


                <div>TODO EXTERNAL LINKS</div>

            </div>


            <div>
                <h2>{{ $manuscript->name }}: Folios</h2>

            </div>
        </div>

        <div x-data="manuscriptShow({
            manuscriptName: '{{ $manuscript->name }}',
            manifest: '{{ route('iiif.presentation.manifest', $manuscript->name) }}'
        })">
            <div class="flex">
                <div class="w-1/2">



                    <div class="w-full">
                        <div class="relative right-0">
                            <ul class="relative flex list-none flex-wrap bg-blue-gray-50/60 p-1" data-tabs="tabs"
                                role="list">
                                <li class="z-30 flex-auto text-center">
                                    <a class="text-slate-700 z-30 mb-0 flex w-full cursor-pointer items-center justify-center rounded-lg border-0 bg-inherit px-0 py-1 transition-all ease-in-out"
                                        data-tab-target="" x-bind:active="!lang" role="tab" aria-selected="true"
                                        active 
                                        id="diplomaticBtn"
                                        @click="lang=''">
                                        <span class="ml-1">{{ 'CSRPC' === $manuscript->name ? '' : 'Diplomatic' }}</span>
                                    </a>
                                </li>
                                @foreach ($manuscript->folios->first()->contentsTranslations as $translation)
                                    <li class="z-30 flex-auto text-center">
                                        <a class="text-slate-700 z-30 mb-0 flex w-full cursor-pointer items-center justify-center rounded-lg border-0 bg-inherit px-0 py-1 transition-all ease-in-out"
                                            data-tab-target="" role="tab" aria-selected="false"
                                            @click="lang='{{ $translation->lang['code'] }}'">
                                            <span class="ml-1">{{ $translation->lang['name'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <template x-if="currentPageUrl">
                        <iframe class="w-full h-[600px]" :src="currentPageUrl + '?lang=' + lang"></iframe>
                    </template>











































                </div>
                <div class="relative w-1/2 h-[600px]">
                    <div id="mirador"></div>
                </div>
            </div>
        </div>


    </div>
@endsection
