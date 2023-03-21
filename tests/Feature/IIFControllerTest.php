<?php

it('test_iiif_image_info', function () {
    $identifier = '51__GA019_f.112v_original.jpg';
    $url = "/iiif/{$identifier}/info.json";
    $response = $this->get($url);
    $response->assertStatus(200);
    // dd($response->decodeResponseJson());
});

it('test_iiif_image_requests', function () {
    $identifier = '51__GA019_f.112v_original.jpg';
    $url = "/iiif/{$identifier}/full/500,/0/default.jpg";
    $response = $this->get($url);
    $response->assertStatus(200);
})->todo();

it('test_iiif_presentation_manifest', function () {
    // config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
    // DB::purge('sqlite');
    $this->createManuscript();
    $url = route('iiif.presentation.manifest', 'GA019');
    $response = $this->get($url);
    $response->assertStatus(200);
});

it('iiif_presentation_collection', function () {
    $this->createManuscript();

    $url = route('iiif.presentation.collection');
    // dd($url);
    // http://localhost/iiif/107__GA05_f.346v_original.jpg/full/5
    // exit("\n".url($url)."\n");
    $response = $this->get($url);
    $response->assertStatus(200);
    // dd($response->decodeResponseJson());
});
