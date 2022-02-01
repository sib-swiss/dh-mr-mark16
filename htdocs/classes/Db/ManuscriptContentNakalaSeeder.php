<?php

namespace classes\Db;

use Base;
use classes\Models\Manuscript;
use Log;
use Nakala;

/**
 * Seed manuscript_contents sqlite table reading from Nakala url collections
 *
 * @author Silvano Aldà / SIB - 2021
 */
class ManuscriptContentNakalaSeeder
{
    private $f3;
    private $logger;
    private $nakalaCollectionURL;

    /**
     * Contructor
     *
     * @param string $nakalaCollectionURL
     */
    public function __construct(string $nakalaCollectionURL)
    {
        $this->f3 = Base::instance();
        $this->logger = new Log(str_replace('\\', '-', __CLASS__) . '.log');
        $this->nakalaCollectionURL = $nakalaCollectionURL;
    }

    /**
     * handle the seeding
     *
     * @return array
     */
    public function handle(): array
    {
        $nakala = new Nakala();
        $nakala->set_url($this->nakalaCollectionURL);

        $manuscript = $this->seedManuscript([
            'name' => strtoupper(str_replace(' ', '', $nakala->get_meta('dcterm-bibliographicCitation'))),
            'content' => $nakala->get_json()
        ]);

        $nakalaFolios = [];
        foreach ($nakala->get_files() as $nakalaFileData) {
            $this->seedManuscriptContentFromNakala($manuscript, $nakalaFileData);
            if ($nakalaFileData->extension === 'xml') {
                $nakalaFolios[] = $nakalaFileData->name;
            }
        }

        //  commented out because this needed only to create sqlite from filesystems, 
        //  never run anymore from now on
        //  $manuscript->seedImagesFromFilesystems();


        if (!($manuscript->temporal > 0)) {
            $manuscript->temporal = $manuscript->getMeta('dcterm-temporal');
            $manuscript->save();
        }

        // Remove orphan folios and its related images
        // example when folios are renamed in nakala
        foreach ($manuscript->contentsFolios() as $manuscriptFolios) {
            if (!in_array($manuscriptFolios->name, $nakalaFolios)) {
                $this->logger->write('DELETE ' . $manuscriptFolios->name . ' NOT IN NAKALA ' . implode("; ", $nakalaFolios));
                $manuscriptFolios->remove();
            }
        }

        // Remove orphan HTML contents (original and translations)
        // example when folios are renamed in nakala
        foreach ($manuscript->contentsHtml() as $contentHtml) {
            $inFolios = false;
            foreach ($nakalaFolios as $folioName) {
                $folioNameNoExtension = substr($folioName, 0, -4);
                if (
                    substr($contentHtml->name, 0, strlen($folioNameNoExtension)) === $folioNameNoExtension
                ) {
                    $inFolios = true;
                    break;
                }
            }
            if (!$inFolios) {
                $this->logger->write('DELETE ' . $contentHtml->name . ' NOT IN FOLIOS ' . implode("; ", $nakalaFolios));
                $contentHtml->erase();
            }
        }

        // clean manuscript from orphan content records and/or files in fs
        $manuscript->clean();

        return [
            'success' => true,
            'data' => json_decode($manuscript->content),
            'encodedId' => $manuscript->getEncodedId()
        ];
    }

    /**
     * seed Manuscript
     *
     * @param [array] $manuscripData
     * @return Manuscript
     */
    public function seedManuscript($manuscripData): Manuscript
    {
        $manuscript = Manuscript::findBy('name', $manuscripData['name']);
        if ($manuscript) {
            //  delete all manuscript related contents

            //  ToDo: do not delete images if added?

            /* avoid loose of partner, image copyright
            $this->f3->GET('DB')->exec(
                'DELETE FROM manuscript_contents WHERE manuscript_id=' . $manuscript->id . ';'
            );
            */

            $manuscript->url = $this->nakalaCollectionURL;
            $manuscript->content = $manuscripData['content'];
            $manuscript->save();
            // $this->logger->write('update ' . $manuscript->name . ': ' . json_encode($manuscripData));
        } else {
            //$this->logger->write('store ' . $manuscripData['name'] . ': ' . json_encode($manuscripData));
            $manuscript = Manuscript::store([
                'name' => $manuscripData['name'],
                'url' => $this->nakalaCollectionURL,
                'content' => $manuscripData['content'],
                'published' => 0
            ]);
        }
        return $manuscript;
    }

    public function seedManuscriptContentFromNakala($manuscript, $nakalaFileData)
    {
        $nakala_parsed_url = parse_url($this->nakalaCollectionURL); // ex. 'https://api.nakala.fr/datas/10.34847/nkl.6f83096n'
        $nakala_download_url = $nakala_parsed_url['scheme'] . '://' . $nakala_parsed_url['host'] . str_replace('datas', 'data', $nakala_parsed_url['path']);
        $url = $nakala_download_url . '/' . $nakalaFileData->sha1;
        $manuscriptContentAttributes = [
            'manuscript_id' => $manuscript->id,
            'name' => $nakalaFileData->name,
            'extension' => strtolower($nakalaFileData->extension),
            'url' => $url,
            'content' => file_get_contents($url)
        ];

        return (new ManuscriptContentSeeder())->save($manuscript, $manuscriptContentAttributes);
    }
}
