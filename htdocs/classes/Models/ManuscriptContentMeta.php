<?php

namespace classes\Models;

use Rdf;

/**
 * ManuscriptContentMeta
 * Dependedencies: Fat free framework
 * @author Silvano Aldà / SIB - 2021
 */
class ManuscriptContentMeta extends ManuscriptContent
{
    public function getContent()
    {
        if (is_file($this->f3->get('MR_DATA_PATH') . '/' . $this->manuscript()->name . '/' . $this->name)) {
            $rdf = new Rdf();
            $rdf->load('manuscripts/' . $this->manuscript()->name . '/' . $this->name);
            $rdf->parse();
            return [
                'data' => $rdf->get_document(),
                'xml' => $rdf->get_xml(true)
            ];
        }

        return $this->content;
    }

    /**
     * return array of manuscript's folio additional languages
     *
     * @return array
     */
    public function contentsTranslations()
    {
        // Define supported languages
        $manuscript_folio_languages = [
            'ENG' => 'English',
            'FRA' => 'French',
            'GER' => 'German'
        ];

        $folioName = $this->getFolioName();
        $return = [];
        foreach ($manuscript_folio_languages as $k => $v) {
            $contentLang = (new ManuscriptContentHtml())
                ->findone(
                    [
                        'manuscript_id=? 
                        AND (extension=? OR extension=?)
                        AND name LIKE ?
                        AND (name like ?)',

                        $this->manuscript()->id,

                        'html',
                        'htm',

                        $folioName . '%',

                        '%_' . $k . '%'
                    ]
                );
            if ($contentLang) {
                $return[] = [
                    'manuscript_id' => $this->manuscript()->id,
                    'lang_abbr' => $k,
                    'lang_ext' => $v,
                    'name' => $contentLang->name,
                    'id' => $contentLang->getEncodedId(),
                    'metaFolioName' => $folioName,
                    'thisFolioName' => $contentLang->getFolioName()
                ];
            }
        }
        return $return;
    }

    /**
     * getTeiUrl
     *
     * @return void
     */
    public function getTeiUrl()
    {
        if ($this->url) {
            return $this->url;
        }
        $foaf_data = $this->getContent()['data']['foaf'];
        return json_decode(json_encode($foaf_data['Document'][0]))->{'@attributes'}->about;
    }

    public function getFolioImage()
    {
        foreach ($this->manuscript()->contentsImage() as $contentImage) {
            if (strpos(str_replace('_', '', $contentImage->name), str_replace('_', '', $this->getFolioName())) !== false) {
                return $contentImage;
            }
        }
    }

    /**
     * get HTML Translations
     *
     * @return [ManuscriptContentHtml]
     */
    public function getTranslations()
    {
        $return = [];
        foreach ($this->manuscript()->contentsHtml() as $contentHtml) {
            if (strpos(str_replace('_', '', $contentHtml->name), str_replace('_', '', $this->getFolioName())) !== false) {
                $return[] = $contentHtml;
            }
        }
        return $return;
    }

    /**
     * remove folios and its related object (images)
     */
    public function remove()
    {
        $folioImage = $this->getFolioImage();
        if ($folioImage) {
            $folioImage->remove();
        }
        $this->erase();
    }
}
