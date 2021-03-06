<?php

namespace pages;

use stdClass;

class AboutResource
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
        $page_options->title = 'About';
        $page_options->mirador = false;
        $page_options->old_code = false;

        // Building page title
        $app_config = $this->f3->get('MR_CONFIG');
        $page_title = $app_config->title->base;
        $page_title .= (isset($page_options->title) && !empty($page_options->title) ? ' / ' . $page_options->title : '');
        $page_title .= ($app_config->debug === true ? ' ' .  html_entity_decode($app_config->title->separator) . ' ' . $app_config->title->end : '');
        $page_title .= ($app_config->debug === true ? ' [Debug Mode]' : '');
        $page_title .= ($app_config->maintenance === true ? ' ' .  html_entity_decode($app_config->title->separator) . ' [Maintenance]' : '');
        $this->f3->set('page_title', $page_title);

        if ($this->f3->get('MR_CONFIG')->maintenance === true) {
            $this->f3->set('template_content', 'pages/templates/maintenance.html');
            echo \Template::instance()->render($this->f3->get('template_layout'));
            return;
        }

        $this->f3->set('page_options', $page_options);

        // Reconstruct request uri
        $request_uri = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === true ? 'https' : 'http');
        $request_uri .= '://';
        $request_uri .= htmlentities(strip_tags($_SERVER['HTTP_HOST']));
        $request_uri .= $this->f3->get('MR_PATH_WEB');
        $this->f3->set('request_uri', $request_uri);

        // f3 template
        $this->f3->set('template_content', 'pages/templates/about.html');
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
