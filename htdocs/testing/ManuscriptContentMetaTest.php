<?php

namespace Tests;

require __DIR__ . '/../vendor/autoload.php';

use classes\Models\Manuscript;
use Test;
use classes\Models\ManuscriptContentMeta;

class ManuscriptContentMetaTest extends TestCase
{
    private $manuscripContent;

    public function setup()
    {
        $this->manuscripContent = new ManuscriptContentMeta();

        $this->test = new Test();
    }

    /**
     * testgetMeta
     *
     * @return void
     */
    public function testgetMeta()
    {
        $testData = [];
        foreach (Manuscript::all() as $manuscript) {
            foreach ($manuscript->contentsFolios() as $folio) {
                //$testData[$manuscript->name]
                foreach ($folio->getTranslations() as $getTranslations) {
                    $getLangCode = $getTranslations->getLangCode();
                    $this->test->expect(
                        $getLangCode !== null,
                        $getTranslations->name . ': Language code is null: ' . $manuscript->getMeta('dcterm-language') ." => ". $getLangCode
                    );
                    $testData[$manuscript->name][$folio->name][$getTranslations->name] = [
                        'langCode' => $getLangCode
                    ];
                }
            }
        }
        return $this->test;
    }
}
