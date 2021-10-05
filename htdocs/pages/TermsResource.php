<?php

namespace pages;

use stdClass;

class TermsResource
{
    public function __construct($f3)
    {
        $this->f3 = $f3;
    }

    public function get()
    {
        $this->f3->set('template_layout', 'ui/templates/layout.html');

        $page_options = new stdClass();
        $page_options->title = 'Terms' ;
        $page_options->mirador = false;
        
        $app_config = $this->f3->get('MR_CONFIG');
        $page_title = $app_config->title->base;
        $this->f3->set('page_title', $page_title);

        if ($this->f3->get('MR_CONFIG')->maintenance === true) {
            $this->f3->set('template_content', 'pages/templates/maintenance.html');
            echo \Template::instance()->render($this->f3->get('template_layout'));
            return;
        }
        
        
        $this->f3->set('template_content', 'pages/templates/terms.html');
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
