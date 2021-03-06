<?php

namespace pages;

use classes\Models\ManuscriptContentHtml;

class ViewResource
{
    public function __construct($f3)
    {
        $this->f3 = $f3;
    }

    public function get()
    {
        if ($this->f3->get('MR_CONFIG')->maintenance === true) {
            $this->f3->set('template_content', 'pages/templates/maintenance.html');
            echo \Template::instance()->render($this->f3->get('template_layout'));
            return;
        }
        $manuscriptContent = ManuscriptContentHtml::findWhere('name', 'like', '%' . base64_decode($this->f3->get('GET.folio')) . '%');
        if ($manuscriptContent === false) {
            dd([
                $this->f3->get('GET.folio'),
                base64_decode($this->f3->get('GET.folio')),
                $this->f3->get('DB')->log()
            ]);
        }
        if (filter_var($this->f3->get('GET.alter'), FILTER_VALIDATE_BOOLEAN) === true && (bool)$this->f3->get('GET.alter') === true) {
            echo $manuscriptContent->getAlteredHtml();
        }
        else {
            echo $manuscriptContent->getHtml();
        }
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
