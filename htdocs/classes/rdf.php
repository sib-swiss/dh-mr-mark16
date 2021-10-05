<?php
/**
 * Custom XML/RDF Parser
 * 
 * @author Jonathan Barda / SIB - 2020
 */

// Main config
if (isset($app_config) && !is_object($app_config)) {
    die('App config can\'t be loaded.');
}

// Main class
class Rdf
{
    // Static config
    private $data_folder = __DIR__ . '/../../data';
    private $raw_xml = '';
    private $parsed_document = [];

    public function load(string $file) {
        if (isset($file) && !empty($file)) {
            return $this->raw_xml = file_get_contents($this->data_folder . '/' . $file);
        }
        else {
            die('File not specified.');
        }
    }

    public function parse(bool $debug = false) {
        if (isset($this->raw_xml) && !empty($this->raw_xml)) {
            // Create XML element
            $xml = new SimpleXMLElement($this->raw_xml);

            // Register RDF namespaces
            // Reference: https://stackoverflow.com/a/10067812

            // xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
            // xmlns:geo="http://www.w3.org/2003/01/geo/wgs84_pos#"
            // xmlns:prv="http://purl.org/net/provenance/ns#"
            // xmlns:doap="http://usefulinc.com/ns/doap#"
            // xmlns:meta="http://example.org/metadata#"
            // xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
            // xmlns:geonames="http://www.geonames.org/ontology#"
            // xmlns:void="http://rdfs.org/ns/void#"
            // xmlns:nkl="http://localhost:8080/pubby/"
            // xmlns:dcterms="http://purl.org/dc/terms/"
            // xmlns:dc="http://purl.org/dc/elements/1.1/"
            // xmlns:p="http://localhost:8080/property/"
            // xmlns:owl="http://www.w3.org/2002/07/owl#"
            // xmlns:ore="http://www.openarchives.org/ore/terms/"
            // xmlns:skos="http://www.w3.org/2004/02/skos/core#"
            // xmlns:units="http://dbpedia.org/units/"
            // xmlns:prvTypes="http://purl.org/net/provenance/types#"
            // xmlns:ir="http://www.ontologydesignpatterns.org/cp/owl/informationrealization.owl#"
            // xmlns:dbpedia="http://localhost:8080/resource/"
            // xmlns:yago="http://localhost:8080/class/yago/"
            // xmlns:foaf="http://xmlns.com/foaf/0.1/"
            // xmlns:xsd="http://www.w3.org/2001/XMLSchema#"

            // Read namespaces data
            $namespaces = $xml->getNamespaces(true);
            if ($debug) {
                echo PHP_EOL . 'Found namespaces: ' . print_r($namespaces, true) . PHP_EOL;
            }

            // Prepare document array
            foreach ($namespaces as $ns_id => $ns_value) {
                $this->parsed_document[$ns_id] = [];
            }

            // TODO: Add missing XML namespace management...

            // Read RDF document
            if ($debug) {
                echo 'Reading RDF blocks: ' . $xml->children($namespaces['rdf'])->count() . PHP_EOL;
            }
            $loop = 0;
            foreach ($xml->children($namespaces['rdf']) as $rdf_doc) {
                // New rdf entry, creating it
                if (@!is_array($this->parsed_document['rdf'][$rdf_doc->getName()])) {
                    $this->parsed_document['rdf'][$rdf_doc->getName()] = []; // new rdf array
                }

                // Add content to the new entry
                if (count($rdf_doc->attributes($namespaces['rdf'])) !== 0) {
                    array_push(
                        $this->parsed_document['rdf'][$rdf_doc->getName()], // source array
                        $rdf_doc->attributes($namespaces['rdf']) // new attributes
                    );
                }
                else {
                    array_push(
                        $this->parsed_document['rdf'][$rdf_doc->getName()], // source array
                        $rdf_doc[0] // new value
                    );
                }

                // Read RDFS Label
                if ($debug) {
                    print PHP_EOL . "Loop [$loop] - Reading RDFS Label: " . $rdf_doc->children($namespaces['rdfs'])->count() . PHP_EOL;
                }
                foreach ($rdf_doc->children($namespaces['rdfs']) as $rdfs) {
                    // New rdfs entry, creating it
                    if (@!is_array($this->parsed_document['rdfs']['rdfs-' . $rdfs->getName()])) {
                        $this->parsed_document['rdfs']['rdfs-' . $rdfs->getName()] = []; // new rdfs array
                    }

                    // Add content to the new entry
                    if (count($rdfs->attributes($namespaces['rdfs'])) !== 0) {
                        array_push(
                            $this->parsed_document['rdfs']['rdfs-' . $rdfs->getName()], // source array
                            $rdfs->attributes($namespaces['rdfs']) // new attributes
                        );
                    }
                    else {
                        array_push(
                            $this->parsed_document['rdfs']['rdfs-' . $rdfs->getName()], // source array
                            $rdfs[0] // new value
                        );
                    }
                }

                // Read FOAF Primary Topic
                if ($debug) {
                    print PHP_EOL . "Loop [$loop] - Reading FOAF Primary Topic: " . $rdf_doc->children($namespaces['foaf'])->count() . PHP_EOL;
                }
                foreach ($rdf_doc->children($namespaces['foaf']) as $foaf_primary_topic) {
                    // New foaf entry, creating it
                    if (@!is_array($this->parsed_document['foaf'][$foaf_primary_topic->getName()])) {
                        $this->parsed_document['foaf'][$foaf_primary_topic->getName()] = []; // new foaf array
                    }

                    // Add content to the new entry
                    if (count($foaf_primary_topic->attributes($namespaces['rdf'])) !== 0) {
                        array_push(
                            $this->parsed_document['foaf'][$foaf_primary_topic->getName()], // source array
                            $foaf_primary_topic->attributes($namespaces['rdf']) // new attributes
                        );
                    }
                    else {
                        array_push(
                            $this->parsed_document['foaf'][$foaf_primary_topic->getName()], // source array
                            $foaf_primary_topic[0] // new value
                        );
                    }
            
                    // Read FOAF Document
                    if ($debug) {
                        print PHP_EOL . "Loop [$loop] - Reading FOAF Document: " . $foaf_primary_topic->children($namespaces['foaf'])->count() . PHP_EOL;
                    }
                    foreach ($foaf_primary_topic->children($namespaces['foaf']) as $foaf_document) {
                        // New foaf entry, creating it
                        if (@!is_array($this->parsed_document['foaf'][$foaf_document->getName()])) {
                            $this->parsed_document['foaf'][$foaf_document->getName()] = []; // new foaf array
                        }

                        // Add content to the new entry
                        if (count($foaf_document->attributes($namespaces['rdf'])) !== 0) {
                            array_push(
                                $this->parsed_document['foaf'][$foaf_document->getName()], // source array
                                $foaf_document->attributes($namespaces['rdf']) // new attributes
                            );
                        }
                        else {
                            array_push(
                                $this->parsed_document['foaf'][$foaf_document->getName()], // source array
                                $foaf_document[0] // new value
                            );
                        }

                        // Read FOAF Document dcterms
                        if ($debug) {
                            print PHP_EOL . "Loop [$loop] - Reading dcterms: " . $foaf_document->children($namespaces['dcterms'])->count() . PHP_EOL;
                        }
                        foreach ($foaf_document->children($namespaces['dcterms']) as $foaf_doc_dcterms) {
                            // New dcterms entry, creating it
                            if (@!is_array($this->parsed_document['dcterms']['dcterm-' . $foaf_doc_dcterms->getName()])) {
                                $this->parsed_document['dcterms']['dcterm-' . $foaf_doc_dcterms->getName()] = []; // new dcterms array
                            }

                            // Add content to the new entry
                            if (count($foaf_doc_dcterms->attributes($namespaces['rdf'])) !== 0) {
                                array_push(
                                    $this->parsed_document['dcterms']['dcterm-' . $foaf_doc_dcterms->getName()], // source array
                                    trim($foaf_doc_dcterms->attributes($namespaces['rdf'])) // new attributes
                                );
                            }
                            else {
                                array_push(
                                    $this->parsed_document['dcterms']['dcterm-' . $foaf_doc_dcterms->getName()], // source array
                                    trim($foaf_doc_dcterms[0]) // new value
                                );
                            }
                        }
                    }
                }

                // Increment loop counter
                $loop++;
            }

            if ($debug) {
                echo PHP_EOL . 'Composed structure:' . PHP_EOL;
                print_r($this->parsed_document);
            }

            return (is_array($this->parsed_document) && isset($this->parsed_document['dcterms']) && count($this->parsed_document['dcterms']) > 0);
        }
        else {
            die('You must load the file first.');
        }
    }

    public function get_document() {
        return (is_array($this->parsed_document) && isset($this->parsed_document['dcterms']) && count($this->parsed_document['dcterms']) > 0
            ? $this->parsed_document
            : false
        );
    }

    public function get_xml(bool $to_html = false) {
        if (isset($this->raw_xml) && !empty($this->raw_xml)) {
            return ($to_html === true ? htmlentities($this->raw_xml) : $this->raw_xml);
        }
        else {
            die('You must load the file first.');
        }
    }

    public function print_xml() {
        if (isset($this->raw_xml) && !empty($this->raw_xml)) {
            print(htmlentities($this->raw_xml));
        }
        else {
            die('You must load the file first.');
        }
    }
}
