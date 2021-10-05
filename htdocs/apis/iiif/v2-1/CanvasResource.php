<?php

namespace iiif21;

class CanvasResource
{
    public function __construct($f3)
    {
        $this->f3 = $f3;
    }

    public function get()
    {
        (new PresentationApi($this->f3->get('PARAMS')))
            ->canvas();
    }

    public function post()
    {
        $this->get();
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
