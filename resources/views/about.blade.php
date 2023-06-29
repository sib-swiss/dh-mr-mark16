@extends('layouts.app')

@section('content')
    <div class="container overflow-auto mx-auto">
        <div>
            <h1>MARK16 Manuscript Room</h1>
            <p class="mb-6">Welcome to the MARK16 manuscript room, MARK16 MR. It is the first part of a <em>virtual
                    research
                    environment</em> (VRE)
                devoted to the last chapter of the Gospel according to Mark, developed in the framework of the Swiss
                National Science Foundation PRIMA project <a href="http://p3.snf.ch/project-179755"
                    target="_blank">MARK16</a>.</p>
            <p class="mb-6">The users will find in it a selection of 61 manuscripts and transcriptions for the study of
                Mark 16.
                Translations are also provided for ancient languages other than Greek and Latin, along with some
                translations of Greek and Latin non-biblical material, like commentaries. MARK16 is a bridge between
                scholars engaged in New Testament textual criticism and exegesis by providing material focusing on a chapter
                well known for its diversity in evidence and readings through centuries. Being inspired by the developments
                of the <a href="http://egora.uni-muenster.de/intf/projekte/ecm_en.shtml" target="_blank"><em>Editio Critica
                        Maior</em></a>, it provides manuscripts in eleven ancient languages.
            </p>
            <p class="mb-6">This project has been led in privileged partnership with the New Testament Virtual Manuscript
                Room (INTF,
                Münster), and we are particularly grateful to Dr. Gregory Paulson for the constant dialogue we have around
                our project and the NTVMR, created by Troy Griffitts. The content of the MARK16 manuscript room is provided
                in partnership with about thirty colleagues, and several academic institutions and libraries, whose logos
                stand on the concerned pages. We are enormously thankful to all of them. Each webpage indicates who
                transcribed, encoded and/or translated the material, entirely available in license <a
                    href="https://creativecommons.org/licenses/by/4.0/deed.fr" target="_blank">CC BY 4.0</a>. The data are
                stored in a collection of the open public depositories <a
                    href="https://admin.dasch.swiss/project/n47nDVTHR2W9U8yjEIcj2Q" target="_blank">DaSCH</a> and <a
                    href="https://www.nakala.fr/" target="_blank">Nakala</a>
                (<a href="https://www.huma-num.fr/" target="_blank">Huma-Num</a>) accompanied with
                metadata, which follows the Dublin Core categories.
            </p>
            <p class="mb-6">
                As the Principal Investigator of this project, I would like to acknowledge the talents and the efforts of
                Mina Monier on this manuscript room, joined by Elisa Nury and Priscille Marschall in the encoding of the
                manuscripts. My warm gratitude extends to our software developers, Jean-Bernard Dugied who started the web
                beta application, and to Jonathan Barda, Core-IT (SIB), who produced the beta version in September 2020. All
                our recognition goes to Silvano Aldà (Core-IT, SIB), who has led the MARK16 API to its final state,
                available on <a href="https://github.com/sib-swiss/dh-mr-mark16" target="_blank">GitHub</a>. Finally, the
                MARK16 MR is also in warm debt towards the constant support of our IT-SIB
                colleagues
            </p>
            <p>Questions or suggestions? Please contact
                <a href="mailto:claire.clivaz@sib.swiss">claire.clivaz@sib.swiss</a>.
            </p>
        </div>
        <div class="text-right mt-4">
            <p>Lausanne, June 2023
                <br>
                Claire Clivaz, PI SNSF MARK16, DH+, SIB
            </p>
        </div>

        <br>

        <br>
        <div class="flex justify-between">
            <div>
                <a href="http://www.snf.ch/" target="_blank">
                    <img src="{{ Vite::asset('resources/images/logo-snsf.png') }}" class="h-20" alt="logo-snsf.png"
                        title="SNSF">
                </a>
            </div>
            <div>
                <a href="https://sib.swiss" target="_blank">
                    <img src="{{ Vite::asset('resources/images/sib_logo2023.svg') }}" class="h-28" alt="sib_logo2023.svg"
                        title="SIB">
                </a>
            </div>
        </div>
        <div class="text-center my-6">
            <span>in collaboration with:</span>
            <br>
            <a href="http://ntvmr.uni-muenster.de/" target="_blank">
                <img src="{{ Vite::asset('resources/images/logo-INTF.jpg') }}" class="inline" width="265"
                    alt="logo-INTF.jpg" title="INTF">
            </a>
            <br><br>
            <a href="https://www.nakala.fr/" target="_blank">
                <img src="{{ Vite::asset('resources/images/logo_nakala.png') }}" class="inline" width="265"
                    alt="logo_nakala.png" title="Nakala">
            </a>
            <br><br>
            <a href="https://admin.dasch.swiss/project/n47nDVTHR2W9U8yjEIcj2Q" target="_blank">
                <img src="{{ Vite::asset('resources/images/DaSCH.png') }}" class="inline" width="265" alt="DaSCH.png"
                    title="DaSCH">
            </a>
        </div>



    </div>
@endsection
