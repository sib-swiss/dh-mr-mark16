<?php

namespace mr\v2_1;

use classes\Models\Manuscript;
use stdClass;

class MiradorResource
{
    public function __construct($f3)
    {
        $this->f3 = $f3;
    }

    public function get()
    {
        // No parameters given, display API schema
        if (count($this->f3->get('GET')) === 0) {
            // Build API response
            $api_response = new stdClass();
            $api_response->custom = true;
            $api_response->spec = '1.0';
            $api_response->syntax = [
                $this->f3->get('REALM') . '/?id={base64 encoded id}',
                $this->f3->get('REALM') . '/&folio={base64 encoded folio id}',
            ];
            $api_response->version = '0.1';

            // Set response as JSON
            header('Content-Type: text/json');

            // Display MR API schema
            echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
            return;
        }

        // Missing 'id' parameter, return 'bad request'
        if ($this->f3->get('GET.folio') || $this->f3->get('GET.id') == null) {
            $this->f3->error(400);
        }

        // Required parameters given, load MR API
        $api_response = new stdClass();

        $manuscript = Manuscript::findByEncodedId($this->f3->get('GET.id'));
        $api_response->files = [];
        $api_response->folios = [];
        foreach ($manuscript->contents() as $content) {
            $api_response->files[] = $content->name;
            if (in_array($content->extension, ['html', 'htm'])) {
                $api_response->folios[] = end(explode('/', $content->name));
            }
        }

        $api_response->status = 'OK'; // Add API response status
        $api_response->statusCode = 200; // Add API response status code
        // Set response as JSON
        header('Content-Type: text/json');

        // Display API features
        echo json_encode($api_response, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        return;
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
