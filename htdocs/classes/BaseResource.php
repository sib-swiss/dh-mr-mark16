<?php

namespace classes;

use Base;

class BaseResource
{
    protected $f3;

    public function __construct()
    {
        $this->f3 = Base::instance();
    }

    /**
     * returnResponse
     *
     * @param  mixed $response
     * @return void
     */
    public function returnResponse($response)
    {
        if (is_string($response)) {
            $this->f3->set('response', $response);
        } else {
            if ($this->f3->get('MR_CONFIG')->debug === true) {
                if (!isset($response->debug)) {
                    $response->debug = [];
                }
                $response->debug['POST'] = $this->f3->get('POST');
                $response->debug['FILES'] = $this->f3->get('FILES');
            }

            $this->f3->set('response', json_encode($response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            if (!$this->f3->get('QUIET') === true) {
                header('Content-Type: text/json');
            }
        }

        echo $this->f3->get('response');
    }
}
