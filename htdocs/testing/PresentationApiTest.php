<?php

namespace Tests;

require __DIR__ . '/../vendor/autoload.php';

use Test;
use classes\Models\Manuscript;

class PresentationApiTest extends TestCase
{
    private $manuscript;

    public function setup()
    {
        // $this->manuscript = Manuscript::findBy('name', 'GA1210');
        $this->test = new Test();
    }

    /**
     * testPresentationManifest
     *
     * @return void
     */
    public function testPresentationManifest()
    {
        $this->setup();

        foreach (Manuscript::all() as $manuscript) {
            //foreach (Manuscript::where('name', 'GA2604') as $manuscript) {
            //foreach (Manuscript::where('name', 'GA1') as $manuscript) {
            $this->f3->mock('GET /api/iiif/2-1/' . $manuscript->name . '/manifest');
            $response = json_decode($this->f3->get('response'));
            $expectations = [
                '@context' => 'http://iiif.io/api/presentation/2/context.json',
                '@type' => 'sc:Manifest',
                'label' => $manuscript->getDisplayname()
                // ToDo More
            ];
            foreach ($expectations as $k => $v) {
                $this->test->expect(
                    $response->$k == $v,
                    "$k, expected '$v', got: '" . $response->$k . "'"
                );
            }

            // assert each folio has different name
            $folioNames = [];
            $fail = false;
            foreach ($response->sequences[0]->canvases as $canvas) {
                $folioName = $canvas->label;
                if (in_array($folioName, $folioNames)) {
                    $fail = true;
                }
                $this->test->expect(
                    !in_array($folioName, $folioNames),
                    $manuscript->name . ': ans not unique foliosnames!'
                );
                array_push($folioNames, $folioName);
            }
            if ($fail) {
                dd([
                    '/api/iiif/2-1/' . $manuscript->name . '/manifest',
                    count($response->sequences[0]->canvases),
                    $folioNames
                ]);
            }
        }

        return $this->test;
    }
}
