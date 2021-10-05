<?php
/**
 * Custom Nakala Parser
 * 
 * @author Jonathan Barda / SIB - 2020
 */

// Main class
class Nakala
{
    // Static config
    // private $data_folder = __DIR__ . '/../../data';
    private $data_url = null;
    private $raw_xml = null;
    private $raw_json = null;
    private $parsed_json = null;
    private $error = null;

    public function set_url(string $url) {
        $sanitized_url = (filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)
            ? filter_var($url, FILTER_SANITIZE_URL)
            : null
        );

        if (!is_null($sanitized_url)) {
            $this->data_url = $sanitized_url;
        }

        return (!is_null($this->data_url) ?: false);
    }

    public function get_url() {
        if (is_null($this->data_url)) {
            die('You must set the URL first!');
        }
        return $this->data_url;
    }

    public function get_xml() {
        if (is_null($this->data_url)) {
            die('You must set the URL first!');
        }

        /* F3::Web */
        $web = \Web::instance();
        $options = [
            'header' => [
                'Accept: application/xml'
            ]
        ];
        $req = $web->request($this->data_url, $options);

        /* Check response */
        if (isset($req['error']) && empty($req['error'])) {
            $this->raw_xml = $req['body'];
        }
        else {
            $this->error = $req['error'];
        }

        return (!is_null($this->raw_xml) ? $this->raw_xml : $this->error);
    }

    public function get_json() {
        if (is_null($this->data_url)) {
            die('You must set the URL first!');
        }

        /* F3::Web */
        $web = \Web::instance();
        $options = [
            'header' => [
                'Accept: application/json'
            ]
        ];
        $req = $web->request($this->data_url, $options);

        /* Check response */
        if (isset($req['error']) && empty($req['error'])) {
            $this->raw_json = $req['body'];
        }
        else {
            $this->error = $req['error'];
        }

        return (!is_null($this->raw_json) ? $this->raw_json : $this->error);
    }

    public function parse_json() {
        if (is_null($this->data_url)) {
            die('You must set the URL first!');
        }

        if (!is_null($this->raw_json)) {
            $this->parsed_json = json_decode($this->raw_json);
        }
        else {
            /* F3::Web */
            $web = \Web::instance();
            $options = [
                'header' => [
                    'Accept: application/json'
                ]
            ];
            $req = $web->request($this->data_url, $options);

            /* Check response */
            if (isset($req['error']) && empty($req['error'])) {
                $this->parsed_json = json_decode($req['body']);
            }
            else {
                $this->error = $req['error'];
            }
        }

        return (!is_null($this->parsed_json) ? $this->parsed_json : $this->error);
    }

    public function get_files() {
        if (is_null($this->parsed_json)) {
            $this->parse_json();
        }
        
        return (property_exists($this->parsed_json, 'files') ? $this->parsed_json->files : false);
    }

    public function get_metas() {
        if (is_null($this->parsed_json)) {
            $this->parse_json();
        }

        return (property_exists($this->parsed_json, 'metas') ? $this->parsed_json->metas : false);
    }

    public function convert_metas() {
        $converted_metas = [];

        if ($raw_metas = $this->get_metas()) {
            foreach ($raw_metas as $meta) {
                // Recreate DCTerms
                $meta_parsed_url = parse_url($meta->{'propertyUri'});
                if (stripos($meta_parsed_url['host'], 'nakala.fr') !== false) {
                    $meta_parsed_type = $meta_parsed_url['fragment'];
                }
                else {
                    $meta_parsed_path = explode('/', $meta_parsed_url['path']);
                    $meta_parsed_type = $meta_parsed_path[(count($meta_parsed_path)-1)];
                }

                switch ($meta_parsed_type) {
                    case 'creator':
                        if (is_object($meta->value) && property_exists($meta->value, 'givenname') && property_exists($meta->value, 'surname')) {
                            $converted_metas[] = ["dcterm-${meta_parsed_type}" => $meta->value->givenname . ' ' . $meta->value->surname];
                        }
                        break;
                    
                    default:
                        $converted_metas[] = ["dcterm-${meta_parsed_type}" => $meta->value];
                        break;
                }
            }
        }

        return (count($converted_metas) > 0 ? $converted_metas : false);
    }

    public function get_meta(string $meta) {
        $found_meta = null;

        if ($converted_metas = $this->convert_metas()) {
            foreach ($converted_metas as $converted_meta) {
                // var_dump($converted_meta); exit;
                // var_dump(array_keys($converted_meta)); exit;
                // var_dump(array_values($converted_meta)); exit;
                if (array_keys($converted_meta)[0] === $meta) {
                    $found_meta = array_values($converted_meta)[0];
                    break;
                }
            }
        }

        return (!is_null($found_meta) ? $found_meta : false);
    }


    public function get_meta_all(string $meta) {
        $found_meta = [];

        if ($converted_metas = $this->convert_metas()) {
            foreach ($converted_metas as $converted_meta) {
                // var_dump($converted_meta); exit;
                // var_dump(array_keys($converted_meta)); exit;
                // var_dump(array_values($converted_meta)); exit;
                if (array_keys($converted_meta)[0] === $meta) {
                    $found_metas[] = array_values($converted_meta)[0];
                }
            }
        }

        return $found_metas;
    }

    public static function getMetaFromJson($rawJson, $metaName)
    {
        $nakala = new static();
        $nakala->set_url('https://www.fakeurl.com/fake');
        $nakala->raw_json=$rawJson;
        return $nakala->get_meta($metaName);
    }


    public static function getMetasFromJson($rawJson, $metaName)
    {
        $nakala = new static();
        $nakala->set_url('https://www.fakeurl.com/fake');
        $nakala->raw_json=$rawJson;
        return $nakala->get_meta_all($metaName);
    }


    public function set_raw_json($rawJson)
    {
        $this->raw_json=$rawJson;
    }
}