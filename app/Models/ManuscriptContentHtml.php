<?php

namespace App\Models;

use DOMDocument;
use DOMXPath;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ManuscriptContentHtml extends ManuscriptContent
{
    protected $table = 'manuscript_contents';

    public function url(bool $alter = false)
    {
        return url('/');
    }

    /**
     * return altered HTML using DOMDocument
     */
    public function getAlteredHtml(): string
    {
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();

        $html = $this->getAlteredHtmlOld();

        $dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);

        // no need of this line, all css are handled in each manuscript itself
        $dom->getElementsByTagName('body')[0]
            ->setAttribute('style', 'width: max-content; background-color: #FAE6C3;');

        // fix mstrans class overriding/adding linine css styles
        $finder = new DOMXPath($dom);
        $classname = 'msstrans';
        foreach ($finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' $classname ')]") as $node) {
            $styles = explode(';', $node->getAttribute('style'));
            $fontFamily = config('manuscript.languages.'.$this->getLangCode().'.font') ?: '';
            array_push(
                $styles,
                'width: max-content',
                ($this->manuscript->name === 'GA099' ? 'min-width: 800px' : ''), // avoid text overlapping in GA 099
                'position: relative', // avoid text overlapping, GA 2937 folio 94r
                'font-size: 100% !important',
                'text-size-adjust: 100% !important',
                'background-color: #FAE6C3',
                ($fontFamily ? 'font-family: "'.$fontFamily.'"' : '')

            );
            $updatesStyle = implode('; ', array_filter($styles));
            $node->setAttribute('style', $updatesStyle);
        }

        $content = $dom->saveHTML($dom->documentElement);

        return $content;
    }

    /**
     * return altered HTML using replace
     *
     * @return string
     */
    public function getAlteredHtmlOld()
    {
        // Detect folio language
        $folio_lang = $this->getLangCode();

        // Load original HTML
        $html = $this->content;

        // Remove weird character
        $html = str_replace(['&#65279;', '﻿'], '', $html);

        // Parse folio id
        // $parsed_folio_id = (!is_null($folio_id) ? explode('_', $folio_id) : null);

        // Rewrite document type to HTML5 specification
        $html = str_replace(
            '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
            '<!DOCTYPE html>',
            $html
        );
        if ($folio_lang) {
            $html = str_replace(
                '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en_US" lang="en_US"',
                '<html lang="'.$folio_lang.'"',
                $html
            );
        }

        // Remove extra comments
        $html = preg_replace('/<!--((?!\[endif\])[\s\S])*?-->/', '', $html);

        // Remove extra code blocks
        $html = str_replace('<link rel="stylesheet" href="add link here" type="text/css">', '', $html);

        // Remove extra browser margin
        $html = str_replace(
            '<html ',
            '<html style="margin-top: -16px;" ',
            $html
        );

        $html = str_replace(
            ['<body>', '<body lang="la">', '<body lang="grc">', '<body style="margin: 0 !important; line-height: 1.5 !important; color: #212529;">'],
            // '<body style="margin: 0 !important; line-height: 1.5 !important; color: #212529; background-color: #FAE6C3;">',
            '<body style="margin: 0 !important; line-height: 1.5 !important; color: #212529"; background-color: #FAE6C3;">',
            $html
        );

        // Remove extra 'head' profile
        $html = str_replace('<head profile="http://www.w3.org/2000/08/w3c-synd/#">', '<head>', $html);

        // Declare missing fonts
        switch ($folio_lang) {
            case 'grc':
            case 'ar':
            case 'am':
            case 'eth':
            case 'cu':
            case 'la':
                $fonts_to_import = PHP_EOL;
                $fonts_to_import .= '@font-face {'.PHP_EOL;
                $fonts_to_import .= "\t".'font-family: \'Gentium Plus\';'.PHP_EOL;
                $fonts_to_import .= "\t".'src: url(\'/community/fonts/GentiumPlus-R.ttf\') format(\'truetype\');'.PHP_EOL;
                $fonts_to_import .= "\t".'font-weight: normal;'.PHP_EOL;
                $fonts_to_import .= "\t".'font-style: normal;'.PHP_EOL;
                $fonts_to_import .= '}'.PHP_EOL;
                break;
            case 'got':
                $fonts_to_import = PHP_EOL;
                $fonts_to_import .= '@font-face {'.PHP_EOL;
                $fonts_to_import .= "\t".'font-family: \'Bokareis\';'.PHP_EOL;
                $fonts_to_import .= "\t".'src: url(\'/community/fonts/Bokareis-Normal.ttf\') format(\'truetype\');'.PHP_EOL;
                $fonts_to_import .= "\t".'font-weight: normal;'.PHP_EOL;
                $fonts_to_import .= "\t".'font-style: normal;'.PHP_EOL;
                $fonts_to_import .= '}'.PHP_EOL;
                break;
            case 'cop':
                $fonts_to_import = PHP_EOL;
                $fonts_to_import .= '@font-face {'.PHP_EOL;
                $fonts_to_import .= "\t".'font-family: \'AntinoouWeb\';'.PHP_EOL;
                $fonts_to_import .= "\t".'src: '
                            .'url("/community/fonts/antinoou-webfont.eot") format("embedded-opentype"),'.PHP_EOL
                            .'url("/community/fonts/antinoou-webfont.woff2") format("woff2"),'.PHP_EOL
                            .'url("/community/fonts/antinoou-webfont.woff") format("woff"),'.PHP_EOL
                            .'url("/community/fonts/antinoou-webfont.ttf") format("truetype"),'.PHP_EOL
                            .PHP_EOL;
                $fonts_to_import .= "\t".'font-weight: normal;'.PHP_EOL;
                $fonts_to_import .= "\t".'font-style: normal;'.PHP_EOL;

                $fonts_to_import .= '}'.PHP_EOL;
                break;

            case 'syc':
            case 'syr':
                $fonts_to_import = PHP_EOL;
                $fonts_to_import .= '@font-face {'.PHP_EOL;
                $fonts_to_import .= "\t".'font-family: \'Noto Sans Syriac\';'.PHP_EOL;
                $fonts_to_import .= "\t".'src: url(\'/community/fonts/NotoSansSyriac-Regular.ttf\') format(\'truetype\');'.PHP_EOL;
                $fonts_to_import .= "\t".'font-weight: normal;'.PHP_EOL;
                $fonts_to_import .= "\t".'font-style: normal;'.PHP_EOL;
                $fonts_to_import .= '}'.PHP_EOL;
                break;
            default:
                $fonts_to_import = '';
                break;
        }

        // Load declared missing fonts
        $html = str_replace(
            ['<style type="text/css">', '<style>'],
            ['<style type="text/css">'.$fonts_to_import, '<style>'.$fonts_to_import],
            $html
        );

        // Patch existing font classes
        $html = str_replace(
            'font-family: Antinoou;',
            'font-family: AntinoouWeb;',
            $html
        );

        // Patch font rendering
        // $html = str_replace(
        //     [
        //         'font-family: Gentium, Gentium, Times, Gentium Plus, Arial Unicode MS;',
        //         'font-family: Gentium, Times, Gentium Plus, Arial Unicode MS;'
        //     ],
        //     // 'font-family: ' . ($folio_lang === 'cop' ? 'AntinoouWeb' : 'Gentium Plus') . ';',
        //     'font-family: ' . (property_exists($this->f3->get('MR_CONFIG')->languages, $folio_lang)
        //         ? "'" . $this->f3->get('MR_CONFIG')->languages->{$folio_lang}->font . "'"
        //         : "'Gentium Plus'") . ';',
        //     $html
        // );

        // handled on line 60
        // $html = str_replace(
        //     '<div class="msstrans"',
        //     '<div class="msstrans" style="width: max-content; font-size: 100% !important; text-size-adjust: 100% !important; background-color: ' . $this->f3->get('MR_CONFIG')->iframe->background . ';"',
        //     $html
        // );

        // Change relative paths
        // $html = url(str_replace('/community/fonts', '/resources/frontend/fonts/NTVMR', $html));

        // some manuscript has already https://ntvmr.uni-muenster.de/community/images... in place, replace only if not the case
        if (strpos($html, 'https://ntvmr.uni-muenster.de/community/images') === false) {
            $html = str_replace('/community/images', 'https://ntvmr.uni-muenster.de/community/images', $html);
        }

        // some manuscript has already https://ntvmr.uni-muenster.de/community/js in place, replace only if not the case
        if (strpos($html, 'https://ntvmr.uni-muenster.de/community/js') === false) {
            $html = str_replace('/community/js', 'https://ntvmr.uni-muenster.de/community/js', $html);
        }

        // view-source:https://api.nakala.fr/data/11280/aae91e3f/33b603d29e5feb17f57bf6705595fb85ddfeeb0a
        // line 300 we have this: <script type="text/javascript" src="file:///C:/community/js/jquery/jquery.min.js"></script>
        $html = str_replace('file:///C:', '', $html);

        // Minor javascript code fix
        // $html = str_replace('$(m).outerHeight()', 'myJQ(m).outerHeight()', $html);
        $html = str_replace(
            ['$(m)', '$(this)', '$(destination)'],
            ['myJQ(m)', 'myJQ(this)', 'myJQ(destination)'],
            $html
        );

        // Replace extra added code by the normal one
        $html = str_replace(
            'focusVerse(event.data.osisID);',
            'displayOptions = event.data;'.PHP_EOL.str_repeat("\t", 2).'updateDisplayFromOptions();',
            $html
        );

        return $html;
    }

    public function lang(): Attribute
    {
        $return_value = [];
        if (str_contains($this->name, '_ENG')) {
            $return_value = ['code' => 'ENG', 'name' => 'English'];
        } elseif (str_contains($this->name, '_FRA')) {
            $return_value = ['code' => 'FRA', 'name' => 'French'];
        } elseif (str_contains($this->name, '_GER')) {
            $return_value = ['code' => 'GER', 'name' => 'German'];
        }

        return Attribute::make(
            get: fn () => $return_value,
        );
    }

    public function getLangCode()
    {
        return $this->manuscript->getLangCode();
    }
}
