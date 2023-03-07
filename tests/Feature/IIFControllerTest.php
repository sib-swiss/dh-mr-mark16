<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class IIFControllerTest extends TestCase
{
    public function test_iiif_image_info(): void
    {
        // http://localhost/iiif/107__GA05_f.346v_original.jpg/info.json
        $identifier = str_replace('/', '__', '107/GA05_f.346v_original.jpg');
        $url = "/iiif/{$identifier}/info.json";
        // exit("\n".url($url)."\n");
        $response = $this->get($url);

        $response->assertStatus(200);
        // dd($response->decodeResponseJson());
    }

    public function test_iiif_image_requests(): void
    {
        // http://localhost/iiif/107__GA05_f.346v_original.jpg/full/500,/0/default.jpg
        $identifier = '122__partner-1616426116.png';

        $identifier = str_replace('/', '__', '107/GA05_f.346v_original.jpg');
        $url = "/iiif/{$identifier}/full/500,/0/default.jpg";
        // exit("\n".url($url)."\n");
        $response = $this->get($url);
        $response->assertStatus(200);
        // dd($response->decodeResponseJson());
    }

    public function test_iiif_presentation_manifest(): void
    {
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        // dd(config('database.connections.sqlite'));
        DB::purge('sqlite');
        $url = route('iiif.presentation.manifest', 'GA05');
        // dd($url);
        // http://localhost/iiif/107__GA05_f.346v_original.jpg/full/5
        // exit("\n".url($url)."\n");
        $response = $this->get($url);
        $response->assertStatus(200);
        // dd($response->decodeResponseJson());
    }

    public function test_iiif_presentation_collection(): void
    {
        config(['database.connections.sqlite.database' => database_path('database.sqlite')]);
        // dd(config('database.connections.sqlite'));
        DB::purge('sqlite');
        $url = route('iiif.presentation.collection');
        // dd($url);
        // http://localhost/iiif/107__GA05_f.346v_original.jpg/full/5
        // exit("\n".url($url)."\n");
        $response = $this->get($url);
        $response->assertStatus(200);
        // dd($response->decodeResponseJson());
    }
}
