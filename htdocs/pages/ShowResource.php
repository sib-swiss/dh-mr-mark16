<?php

namespace pages;

use classes\BaseResource;
use classes\Models\Manuscript;
use stdClass;

class ShowResource extends BaseResource
{
    public function get()
    {
        $manuscript = Manuscript::findByEncodedId($this->f3->get('GET.id'));
        if (!$manuscript) {
            return $this->returnResponse('Manuscript Not found');
        }

        if ((bool)$manuscript->published !== true) {
            return $this->returnResponse('Manuscript Not Pulished');
        }
        $this->f3->set('template_layout', 'ui/templates/layout.html');

        // page options
        $page_options = new stdClass();
        $page_options->title = 'Show / ' . $manuscript->getDisplayname();
        $page_options->mirador = true;

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
            return $this->returnResponse(\Template::instance()->render($this->f3->get('template_layout')));
        }

        // Navigation pages
        $navigation_pages = [
            ['href' => 'about', 'name' => 'About'],
            ['href' => 'content', 'name' => 'Content'],
            ['href' => 'search', 'name' => 'Advanced Search']
        ];
        $this->f3->set('navigation_pages', $navigation_pages);

        // Display manuscript image in the list
        // $page_options->display_list_images = false;
        // $page_options->authorized_extensions = ['jpg', 'jpeg'];

        // Set page options
        $this->f3->set('page_options', $page_options);

        // Manuscript Folio ID
        $this->f3->set('manuscript_folio_id', $this->f3->get('GET.folio') ? htmlentities(strip_tags(base64_decode($this->f3->get('GET.folio')))) : null);

        // Securing query string gathered from web server
        if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
            parse_str(htmlentities(strip_tags($_SERVER['QUERY_STRING'])), $parsed_query_string);
        } else {
            $parsed_query_string = [];
        }
        // Rebuild and sanitize request uri
        $request_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === true ? 'https' : 'http');
        $request_uri .= '://';
        $request_uri .= isset($_SERVER['HTTP_HOST']) ? htmlentities(strip_tags($_SERVER['HTTP_HOST'])) : '';
        $request_uri .= htmlentities(strip_tags(str_replace('/index.php', '/view', $_SERVER['SCRIPT_NAME'])));
        $request_uri .= (isset($parsed_query_string['id']) ? '?id=' . $parsed_query_string['id'] : '');
        $this->f3->set('request_uri', $request_uri);

        //dd($manuscript_partner_image);

        $this->f3->set('manuscript', $manuscript);
        $this->f3->set('manuscript_partner_url', $manuscript->getPartnerUrl());
        $this->f3->set('manuscript_partners_image', $manuscript->contentPartners());

        $this->f3->set('contentsHtml', $manuscript->contentsHtml());
        $this->f3->set('contentsMeta', $manuscript->contentsMeta());
        $this->f3->set('contentsTranslations', $manuscript->contentsMeta()[0]->contentsTranslations());

        // f3 template
        $this->f3->set('template_content', 'pages/templates/show.html');
        return $this->returnResponse(\Template::instance()->render($this->f3->get('template_layout')));
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
