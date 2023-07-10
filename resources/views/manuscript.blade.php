@extends('layouts.app')

@section('content')
    <div class="mx-2">

        <div class="p-5">
            <h1 class="my-0 mb-3 pb-3 flex align-middle gap-2 border-b-2 border-gray-800 text-3xl">
                <a href="javascript:location.reload()"
                    class="text-blue-800 hover:underline">{{ $manuscript->getDisplayname() }}</a>
                <small>
                    @if ('CSRPC' === $manuscript->name)
                        Printed edition ©
                    @else
                        Images ©
                    @endif
                </small>
                </a>


                @foreach ($manuscript->getMedia('partners') as $partner)
                    <a href="{{ $partner->getCustomProperty('url') ? $partner->getCustomProperty('url') : '#' }}"
                        style="text-decoration: none;" target="_blank">
                        <img src="{{ $partner->getUrl() }}" alt="{{ $manuscript->getMeta('provenance') }}"
                            style="max-width: 150px; max-height: 150px;">
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

            <div class="lg:flex justify-between pb-3  border-b border-black">
                <div class="w-1/3">
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


                <div class="w-1/3">
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

                        @foreach ($manuscript->getMetas('creator') as $creator)
                            <dcterms:creator>{{ $creator['value']['fullName'] }}</dcterms:creator>
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
                        <a id="ddb-hybrid" class="btn_blue" role="button" target="_blank"
                            href="{{ str_replace(['api.', 'datas/'], '', $manuscript->url) }}">
                            metadata
                        </a>
                    </p>
                    @if ($manuscript->getMeta('hasFormat'))
                        <p>
                            <span class="show-metadata">DaSCH: </span>
                            <a class="btn_blue" role="button" target="_blank" href="{!! $manuscript->getMeta('hasFormat') !!}">
                                metadata
                            </a>
                        </p>
                    @endif

                </div>


                <div class="w-1/3">
                    <div class="flex justify-end">

                        <div>
                            <button data-ripple-light="true" data-popover-target="menu_folios" class="btn_blue"
                                {{-- close other menu if open --}}
                                onClick="if(document.querySelector('[data-popover=\'menu_html\']').classList.contains('opacity-1')) {
                                            document.querySelector('[data-popover-target=\'menu_html\']').click()
                                        }">
                                FOLIOS
                            </button>
                            <ul role="menu" data-popover="menu_folios" data-popover-placement="bottom-end"
                                class="absolute z-50 min-w-[180px] overflow-auto rounded-md border border-blue-gray-50 bg-white p-3 font-sans text-sm font-normal text-blue-gray-500 shadow-lg shadow-blue-gray-500/10 focus:outline-none">
                                @foreach ($manuscript->folios as $folio)
                                    <li role="menuitem"
                                        class="block w-full cursor-pointer select-none rounded-md px-3 pt-[9px] pb-2 text-start leading-tight transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">

                                        <a class="dropdown-item" href="{{ $folio->getTeiUrl() }}"
                                            target="_blank">{{ $folio->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>


                        <div>
                            <button data-ripple-light="true" data-popover-target="menu_html" class="btn_blue z-50"
                                {{-- close other menu if open --}}
                                onClick="if(document.querySelector('[data-popover=\'menu_folios\']').classList.contains('opacity-1')) {
                                    document.querySelector('[data-popover-target=\'menu_folios\']').click()
                                }">
                                HTML
                            </button>
                            <ul role="menu" data-popover="menu_html" data-popover-placement="bottom-start"
                                class="absolute z-50 min-w-[180px] overflow-auto rounded-md border border-blue-gray-50 bg-white p-3 font-sans text-sm font-normal text-blue-gray-500 shadow-lg shadow-blue-gray-500/10 focus:outline-none">
                                @foreach ($manuscript->contentsHtml as $contentsHtml)
                                    <li role="menuitem"
                                        class="block w-full cursor-pointer select-none rounded-md px-3 pt-[9px] pb-2 text-start leading-tight transition-all hover:bg-blue-gray-50 hover:bg-opacity-80 hover:text-blue-gray-900 focus:bg-blue-gray-50 focus:bg-opacity-80 focus:text-blue-gray-900 active:bg-blue-gray-50 active:bg-opacity-80 active:text-blue-gray-900">

                                        <a class="dropdown-item" href="{{ $contentsHtml->url }}" target="_blank">
                                            {{ $contentsHtml->name }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>


                        <a class="btn_blue" role="button" target="_blank"
                            href="{{ $manuscript->getMeta('isReferencedBy') }}">
                            Bibliography
                        </a>



                        <button data-ripple-light="true" data-dialog-target="dialog" class="btn_blue">
                            Abstract
                        </button>



                        <div class="opacity-0"></div>
                    </div>

                </div>

            </div>
        </div>

        <div x-data="manuscriptShow({
            manuscriptName: '{{ $manuscript->name }}',
            manifest: '{{ route('iiif.presentation.manifest', $manuscript->name) }}'
        })">
            <div class="lg:flex">
                <div class="lg:w-1/2">



                    <div class="w-full">
                        <div class="relative right-0">
                            <ul class="relative flex list-none flex-wrap bg-blue-gray-50/60 p-1" data-tabs="tabs"
                                role="list">
                                <li class="z-30 flex-auto text-center">
                                    <a class="text-slate-700 z-30 mb-0 flex w-full cursor-pointer items-center justify-center rounded-lg border-0 bg-inherit px-0 py-1 transition-all ease-in-out"
                                        data-tab-target="" x-bind:active="!lang" role="tab" aria-selected="true"
                                        active id="diplomaticBtn" @click="lang=''">
                                        <span class="ml-1">{{ 'CSRPC' === $manuscript->name ? 'Transliteration' : 'Diplomatic' }}</span>
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
                <div class="relative lg:w-1/2 h-[600px]">
                    <div id="mirador"></div>
                </div>
            </div>
        </div>




        <div data-dialog-backdrop="dialog" data-dialog-backdrop-close="true"
            class="pointer-events-none fixed inset-0 z-[9999] grid h-screen w-screen place-items-center bg-black bg-opacity-60 opacity-0 backdrop-blur-sm transition-opacity duration-300">
            <div data-dialog="dialog"
                class="relative m-4  min-w-[40%] max-w-[80%] rounded-lg bg-white font-sans text-base  leading-relaxed  antialiased shadow-2xl">
                <div
                    class="flex shrink-0 items-center p-4 font-sans text-2xl font-semibold leading-snug text-blue-gray-900 antialiased">
                    Abstract
                </div>
                <div
                    class="relative border-t border-b border-t-blue-gray-100 border-b-blue-gray-100 p-4 font-sans text-base leading-relaxed  antialiased">
                    {{ $manuscript->getMeta('abstract') }}
                </div>
                <div class="flex shrink-0 flex-wrap items-center justify-end p-4 ">
                    <button data-ripple-dark="true" data-dialog-close="true" class="btn_blue">
                        Close
                    </button>
                </div>
            </div>
        </div>



    </div>
@endsection
