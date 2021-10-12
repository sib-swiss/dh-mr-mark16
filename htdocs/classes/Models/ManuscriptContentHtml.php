<?php

namespace classes\Models;

/**
 * ManuscriptContentHtml
 * Dependedencies: Fat free framework
 * @author Silvano Aldà / SIB - 2021
 */
class ManuscriptContentHtml extends ManuscriptContent
{
    /**
     * Return Manuscript language code
     * ex. grc for Ancient Greek
     * 
     * @todo make it cleaner
     *
     * @return string
     */
    public function getLangCode()
    {
        /* preg_match('/lang="([^"]*)"/', $this->content, $matches);
        if (!isset($matches[1])) {
            return;
        }
        return $matches[1]; */

        // Use the language code from metadata.
        // Because we can't blindly trusting the HTML lang attribute
        return $this->manuscript()->getLangCode();
    }

    /**
     * return altered HTML
     *
     * @return string
     */
    public function getAlteredHtml()
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
                '<html lang="' . $folio_lang . '"',
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
            ['<body>', '<body lang="la">', '<body lang="grc">'],
            // '<body style="margin: 0 !important; line-height: 1.5 !important; color: #212529; background-color: #FAE6C3;">',
            '<body style="margin: 0 !important; line-height: 1.5 !important; color: ' . $this->f3->get('MR_CONFIG')->iframe->color . '; background-color: ' . $this->f3->get('MR_CONFIG')->iframe->background . ';">',
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
                $fonts_to_import  = PHP_EOL;
                $fonts_to_import .= '@font-face {' . PHP_EOL;
                $fonts_to_import .= "\t" . 'font-family: \'Gentium Plus\';' . PHP_EOL;
                $fonts_to_import .= "\t" . 'src: url(\'resources/fonts/MR/Gentium_Plus/GentiumPlus-R.ttf\') format(\'truetype\');' . PHP_EOL;
                $fonts_to_import .= "\t" . 'font-weight: normal;' . PHP_EOL;
                $fonts_to_import .= "\t" . 'font-style: normal;' . PHP_EOL;
                $fonts_to_import .= '}' . PHP_EOL;
                break;
            case 'got':
                $fonts_to_import  = PHP_EOL;
                $fonts_to_import .= '@font-face {' . PHP_EOL;
                $fonts_to_import .= "\t" . 'font-family: \'Bokareis\';' . PHP_EOL;
                $fonts_to_import .= "\t" . 'src: url(\'resources/fonts/MR/Bokareis/Bokareis-Normal.ttf\') format(\'truetype\');' . PHP_EOL;
                $fonts_to_import .= "\t" . 'font-weight: normal;' . PHP_EOL;
                $fonts_to_import .= "\t" . 'font-style: normal;' . PHP_EOL;
                $fonts_to_import .= '}' . PHP_EOL;
                break;
            case 'cop':
                $fonts_to_import  = '';
                /* $fonts_to_import  = PHP_EOL;
                $fonts_to_import .= '@font-face {' . PHP_EOL;
                $fonts_to_import .= "\t" . 'font-family: \'Noto Sans Coptic\';' . PHP_EOL;
                $fonts_to_import .= "\t" . 'src: url(\'/resources/fonts/MR/Coptic/NotoSansCoptic-Regular.ttf\') format(\'truetype\');' . PHP_EOL;
                $fonts_to_import .= "\t" . 'font-weight: normal;' . PHP_EOL;
                $fonts_to_import .= "\t" . 'font-style: normal;' . PHP_EOL;
                $fonts_to_import .= '}' . PHP_EOL; */
                break;

            default:
                $fonts_to_import  = '';
                break;
        }
        /* $fonts_to_import  = PHP_EOL;
        $fonts_to_import .= '@font-face {' . PHP_EOL;
        $fonts_to_import .= "\t" . 'font-family: \'Gentium Plus\';' . PHP_EOL;
        $fonts_to_import .= "\t" . 'src: url(\'resources/fonts/MR/Gentium_Plus/GentiumPlus-R.ttf\') format(\'truetype\');' . PHP_EOL;
        $fonts_to_import .= "\t" . 'font-weight: normal;' . PHP_EOL;
        $fonts_to_import .= "\t" . 'font-style: normal;' . PHP_EOL;
        $fonts_to_import .= '}' . PHP_EOL;
        $fonts_to_import .= '@font-face {' . PHP_EOL;
        $fonts_to_import .= "\t" . 'font-family: \'Bokareis\';' . PHP_EOL;
        $fonts_to_import .= "\t" . 'src: url(\'resources/fonts/MR/Bokareis/Bokareis-Normal.ttf\') format(\'truetype\');' . PHP_EOL;
        $fonts_to_import .= "\t" . 'font-weight: normal;' . PHP_EOL;
        $fonts_to_import .= "\t" . 'font-style: normal;' . PHP_EOL;
        $fonts_to_import .= '}' . PHP_EOL;
        $fonts_to_import .= '@font-face {' . PHP_EOL;
        $fonts_to_import .= "\t" . 'font-family: \'Noto Sans Coptic\';' . PHP_EOL;
        $fonts_to_import .= "\t" . 'src: url(\'/resources/fonts/MR/Coptic/NotoSansCoptic-Regular.ttf\') format(\'truetype\');' . PHP_EOL;
        $fonts_to_import .= "\t" . 'font-weight: normal;' . PHP_EOL;
        $fonts_to_import .= "\t" . 'font-style: normal;' . PHP_EOL;
        $fonts_to_import .= '}' . PHP_EOL; */

        // Load declared missing fonts
        $html = str_replace(
            ['<style type="text/css">', '<style>'],
            ['<style type="text/css">' . $fonts_to_import, '<style>' . $fonts_to_import],
            $html
        );

        // Patch existing font classes
        $html = str_replace(
            'font-family: Antinoou;',
            'font-family: AntinoouWeb;',
            $html
        );

        // Patch font rendering
        $html = str_replace(
            [
                'font-family: Gentium, Gentium, Times, Gentium Plus, Arial Unicode MS;',
                'font-family: Gentium, Times, Gentium Plus, Arial Unicode MS;'
            ],
            // 'font-family: ' . ($folio_lang === 'cop' ? 'AntinoouWeb' : 'Gentium Plus') . ';',
            'font-family: ' . (property_exists($this->f3->get('MR_CONFIG')->languages, $folio_lang)
                ? "'" . $this->f3->get('MR_CONFIG')->languages->{$folio_lang}->font . "'"
                : "'Gentium Plus'") . ';',
            $html
        );

        $html = str_replace(
            '<div class="msstrans"',
            '<div class="msstrans" style="font-size: 100% !important; text-size-adjust: 100% !important; background-color: ' . $this->f3->get('MR_CONFIG')->iframe->background . ';"',
            $html
        );

        // Change relative paths
        $html = str_replace('/community/fonts', '/resources/fonts/NTVMR', $html);

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
            'displayOptions = event.data;' . PHP_EOL . str_repeat("\t", 2) . 'updateDisplayFromOptions();',
            $html
        );

        return $html;
    }

    /**
     * return original HTML
     *
     * @return string
     */
    public function getHtml()
    {
        $html = $this->content;

        return $html;
    }
}
