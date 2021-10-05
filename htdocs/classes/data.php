<?php
/**
 * Data Reader Prototype
 * 
 * This Data Reader will parse the content of the "/data" folder
 * 
 * Supported file types:
 * - HTML
 * - XML
 * - JSON
 * - GIF
 * - PNG
 * - JPEG
 * 
 * Framework used:
 * - none
 * 
 * @author Jonathan BARDA / SIB - 2020
 */

// Main config
if (isset($app_config) && !is_object($app_config)) {
    die('App config can\'t be loaded.');
}

// Main class
class Data {
    // Static config
    private $data_folder = __DIR__ . '/../../data';
    private $active_folder = null;
    private $authorized_extensions = ['xml', 'htm', 'html', 'json', 'gif', 'png', 'jpg', 'jpeg'];
    private $file_list = [];
    private $full_path = false;
    private $analyzed_content = [];

    // Set active folder
    public function initialize(string $folder = '') {
        // Reset internal data store
        $this->reset();

        // Check defined folder
        if (isset($folder) && !empty($folder)) {
            // Read defined folder
            $this->read($this->data_folder . '/' . $folder);

            // Save active folder
            $this->active_folder = $this->data_folder . '/' . $folder;
        }

        // Nothing defined
        else {
            // Read default folder
            $this->read();

            // Save active folder
            $this->active_folder = $this->data_folder;
        }
    }

    // Internal read method
    // private function read(string $folder = '', bool $store_full_path = false) {
    private function read(string $folder = '') {
        // Check folder argument
        if (empty($folder)) {
            $active_folder = $this->data_folder; // No argument given, reading from static config
        }

        // Folder argument given
        else {
            // $active_folder = $this->data_folder . '/' . $folder; // Setting new active folder
            $active_folder = $folder; // Setting new active folder
        }

        // Check new defined active folder
        if (empty($active_folder)) {
            die('No data folder defined.');
        }

        // Active folder defined
        else {
            // Check type, must be an existing folder
            if (is_dir($active_folder)) {
                foreach (glob($active_folder . '/*', GLOB_ERR) as $filename) {
                    $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    
                    // Subfolder found - going recursive
                    if (is_dir($filename)) {
                        // $this->read($filename, $this->full_path);
                        $this->read($filename);
                    }

                    // Analyse content found from authorized types
                    elseif (is_file($filename) && in_array($file_extension, $this->authorized_extensions)) {
                        $this->store_and_parse($filename);
                    }
                }
            }

            // Folder not found
            else {
                die('No data folder found.');
            }
        }
    }

    // Internal file type detection method
    private function store_and_parse(string $file) {
        // Store and parse found files
        if ($this->full_path === true) {
            array_push($this->file_list, $file);

            // Parse given file
            $this->parse($file);
        }
        else {
            array_push($this->file_list, str_replace($this->data_folder . '/', '', $file));

            // Parse given file
            $this->parse(str_replace($this->data_folder . '/', '', $file));
        }
    }

    // Internal filename parsing method
    private function parse(string $file) {
        if ($this->full_path === true) {
            $directory_levels = explode('/', str_replace($this->data_folder . '/', '', $file));
            $mime_type = mime_content_type($file);
            $file_size = filesize($file);
        }
        else {
            $directory_levels = explode('/', $file);
            $mime_type = mime_content_type($this->data_folder . '/' . $file);
            $file_size = filesize($this->data_folder . '/' . $file);
        }

        if (count($directory_levels) >= 4) {
            list($type, $item, $sub_folder, $content) = $directory_levels;
        }
        else {
            list($type, $item, $content) = $directory_levels;
        }

        $parsed_data = [
            'type' => $type,
            'item' => $item,
            'sub_folder' => (isset($sub_folder) ? $sub_folder : null),
            'content' => $content,
            'mime_type' => $mime_type,
            'size' => $file_size
        ];

        array_push($this->analyzed_content, $parsed_data);
    }

    // Print stored file list
    public function get_file_list() {
        // Reset internal data store
        // $this->reset();

        // Read defined folder
        // $this->read();

        // Force initialize
        if (!is_array($this->file_list) || count($this->file_list) === 0) {
            die('You must initialize first!');
        }

        // Print found files
        if (is_array($this->file_list) && count($this->file_list) > 0) {
            echo '<ul>' . PHP_EOL;

            foreach ($this->file_list as $file) {
                if ($this->full_path === true) {
                    echo '<li>' . PHP_EOL;
                    echo '<strong>File:</strong> ' . $file . PHP_EOL;
                    echo '<ul>' . PHP_EOL;
                    echo '<li><strong>Size:</strong> ' . filesize($file) . ' bytes</li>' . PHP_EOL;
                    echo '<li><strong>Type:</strong> ' . mime_content_type($file) . '</li>' . PHP_EOL;
                    echo '</ul>' . PHP_EOL;
                    echo '</li>' . PHP_EOL;
                }
                else {
                    echo '<li>' . PHP_EOL;
                    echo '<strong>File:</strong> ' . $file . PHP_EOL;
                    echo '<ul>' . PHP_EOL;
                    echo '<li><strong>Size:</strong> ' . filesize($this->data_folder . '/' . $file) . ' bytes</li>' . PHP_EOL;
                    echo '<li><strong>Type:</strong> ' . mime_content_type($this->data_folder . '/' . $file) . '</li>' . PHP_EOL;
                    echo '</ul>' . PHP_EOL;
                    echo '</li>' . PHP_EOL;
                }
            }

            echo '</ul>' . PHP_EOL;
        }
        else {
            return false;
        }
    }

    // Print / Return analyzed content
    public function get_structure(bool $print = false) {
        // Reset internal data store
        // $this->reset();

        // Read defined folder
        // $this->read();

        // Force initialize
        if (!is_array($this->analyzed_content) || count($this->analyzed_content) === 0) {
            die('You must initialize first!');
        }

        if ($print === true) {
            // Show structure
            print_r($this->analyzed_content);
        }
        else {
            return $this->analyzed_content;
        }
    }

    // Print defined data folder
    public function print_config() {
        echo 'Reading from: <strong>' . basename((is_null($this->active_folder) ? $this->data_folder : $this->active_folder)) . '</strong>' . PHP_EOL;
    }

    // Count given analyzed content type
    public function count($data_type) {
        // Reset internal data store
        // $this->reset();

        // Read defined folder
        // $this->read();

        // Force initialize
        if (!is_array($this->analyzed_content) || count($this->analyzed_content) === 0) {
            die('You must initialize first!');
        }

        // Temporary storage
        $temp_array = [];

        // Iterate over analyzed content
        foreach ($this->analyzed_content as $analyzed) {
            switch ($analyzed['type']) {
                case strtolower($data_type):
                    $temp_array[] = $analyzed['item'];
                    break;
                
                default:
                    # code...
                    break;
            }
        }

        // Create unique array
        $unique_elements = array_unique($temp_array);

        // Counted data
        $result = new stdClass();
        $result->data_type = $data_type;
        $result->total = count($unique_elements);

        return $result;
    }

    // Reset file list
    public function reset() {
        $this->file_list = [];
        $this->analyzed_content = [];
        return (count($this->file_list) === 0 && count($this->analyzed_content) === 0 ? true : false);
    }

    // Display class related debug info
    public function debug() {
        var_dump(__CLASS__, $this->data_folder, $this->authorized_extensions);
    }
}
