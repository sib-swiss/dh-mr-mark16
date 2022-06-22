<?php

namespace pages;

use classes\Models\Manuscript;
use stdClass;

class ResultsResource
{
    public function __construct($f3)
    {
        $this->f3 = $f3;
    }

    public function get()
    {
        $this->f3->set('template_layout', 'ui/templates/layout.html');

        // page options
        $page_options = new stdClass();
        $page_options->title = 'Search Results';
        $page_options->mirador = false;
        $page_options->old_code = false;

        // Building page title
        $app_config = $this->f3->get('MR_CONFIG');
        $page_title = $app_config->title->base;
        $page_title .= (isset($page_options->title) && !empty($page_options->title) ? ' / ' . $page_options->title : '');
        $page_title .= ($app_config->debug === true ? ' ' . html_entity_decode($app_config->title->separator) . ' ' . $app_config->title->end : '');
        $page_title .= ($app_config->debug === true ? ' [Debug Mode]' : '');
        $page_title .= ($app_config->maintenance === true ? ' ' . html_entity_decode($app_config->title->separator) . ' [Maintenance]' : '');
        $this->f3->set('page_title', $page_title);

        // Show maintenance content if enabled
        if ($this->f3->get('MR_CONFIG')->maintenance === true) {
            $this->f3->set('template_content', 'pages/templates/maintenance.html');
            echo \Template::instance()->render($this->f3->get('template_layout'));
            return;
        }

        // Set page options
        $this->f3->set('page_options', $page_options);

        // Reconstruct request uri
        $request_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === true ? 'https' : 'http');
        $request_uri .= '://';
        $request_uri .= htmlentities(strip_tags($_SERVER['HTTP_HOST']));
        $request_uri .= $this->f3->get('MR_PATH_WEB');
        $this->f3->set('request_uri', $request_uri);

        $subject = $this->f3->get('GET.subject') ? strip_tags(trim(urldecode($this->f3->get('GET.subject')))) : '';
        $keywords = $this->f3->get('GET.keywords') ? strip_tags(trim(urldecode($this->f3->get('GET.keywords')))) : '';
        $this->f3->set('keywords', $keywords);

        $title = $this->f3->get('GET.title') ? strip_tags(trim(urldecode($this->f3->get('GET.title')))) : '';
        $shelfmark = $this->f3->get('GET.shelfmark') ? strip_tags(trim(urldecode($this->f3->get('GET.shelfmark')))) : '';
        $docId = $this->f3->get('GET.docId') ? strip_tags(trim(urldecode($this->f3->get('GET.docId')))) : '';
        $language = $this->f3->get('GET.language') ? strip_tags(trim(urldecode($this->f3->get('GET.language')))) : '';

        $manuscripts = [];
        if (count($this->f3->get('GET')) === 1 && !empty($subject)) {
            $manuscripts = Manuscript::where('name', 'like', '%' . str_replace(' ', '', $subject) . '%');
            $this->f3->set('subject', $subject);
        } else {
            foreach (Manuscript::all(['order' => 'temporal ASC']) as $manuscript) {
                if ((!empty($keywords) && stripos($manuscript->getMeta('dcterm-abstract'), $keywords) !== false) ||
                    (!empty($title) && stripos($manuscript->getDisplayname(), $title) !== false) ||
                    (!empty($shelfmark) && stripos($manuscript->getMeta('dcterm-isFormatOf'), $shelfmark) !== false) ||
                    (!empty($docId) && stripos($manuscript->getMeta('dcterm-temporal'), $docId) !== false) ||
                    (!empty($language) && stripos($manuscript->getLangExtended(), $language) !== false)) {
                    $manuscripts[] = $manuscript;
                }
            }
        }
        $this->f3->set('manuscripts', $manuscripts);
        $this->f3->set('found_manuscripts', count($manuscripts));

        $this->f3->set('template_content', 'pages/templates/results.html');
        echo \Template::instance()->render($this->f3->get('template_layout'));
        return;
    }

    public function post()
    {
        $this->f3->error(405);
    }

    public function put()
    {
        $this->f3->error(405);
    }

    public function delete()
    {
        $this->f3->error(405);
    }
}
