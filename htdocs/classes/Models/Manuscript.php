<?php

namespace classes\Models;

use Base;
use classes\Db\ManuscriptContentSeeder;
use DB\SQL\Mapper;
use Log;
use Nakala;
use Rdf;
use stdClass;

/**
 * This class is responsible of managing manuscript properties and method
 * Dependedencies: Fat free framework
 * @author Silvano Aldà / SIB - 2021
 */
class Manuscript extends Mapper
{
    use ModelTrait;
    protected $attributes = ['id', 'name', 'published'];

    private $f3;
    private $logger;
    public $page;

    /**
     * Constructor
     * https://fatfreeframework.com/3.7/databases#CRUD(ButWithaLotofStyle)
     */
    public function __construct()
    {
        $this->f3 = Base::instance();
        parent::__construct($this->f3->get('DB'), 'manuscripts');

        $this->logger = new Log(str_replace('\\', '-', __CLASS__) . '.log');
        //$this->page = $manuscripPage;
    }

    /**
     * return array of manuscript's contents
     *
     * @return array
     */
    public function contents(): array
    {
        return (new ManuscriptContent())->find(['manuscript_id=?', $this->id], ['order' => 'name ASC']);
    }

    /**
     * return array of manuscript's xml contents
     *
     * @return ManuscriptContentMeta[]
     */
    public function contentsFolios(): array
    {
        return (new ManuscriptContentMeta())
            ->find(
                ['manuscript_id=? AND extension=? ', $this->id, 'xml'],
                ['order' => 'name ASC']
            );
    }

    /**
     * return array of manuscript's html contents
     *
     * @return [ManuscriptContentMeta]
     */
    public function contentsMeta(): array
    {
        return (new ManuscriptContentMeta())
            ->find(
                ['manuscript_id=? AND extension=?', $this->id, 'xml'],
                ['order' => 'name ASC']
            );
    }

    /**
     * return first content with extension xml
     *
     * @return ManuscriptContentMeta|bool
     */
    public function firstContentMeta()
    {
        return (new ManuscriptContentMeta())
            ->findone(
                ['manuscript_id=? AND extension=?', $this->id, 'xml'],
                ['order' => 'name ASC']
            );
    }

    /**
     * return array of manuscript's html contents
     *
     * @return ManuscriptContentHtml[]
     */
    public function contentsHtml(): array
    {
        return (new ManuscriptContentHtml())
            ->find(
                ['manuscript_id=? AND (extension=? OR extension=?)', $this->id, 'html', 'htm'],
                ['order' => 'name ASC']
            );
    }



    /**
     * return array of manuscript's images contents from sqlite
     *
     * @return ManuscriptContentImage[]
     */
    public function contentsImage(array $authorizedEextensions = ['jpg', 'jpeg']): array
    {
        return (new ManuscriptContentImage())
            ->find(
                [
                    'manuscript_id=? 
                    AND (extension=? OR extension=?)
                    AND name NOT LIKE "%partner%"',
                    $this->id,
                    ...$authorizedEextensions
                ],
                ['order' => 'name ASC']
            );
    }

    /**
     * Return array of images found in data  folder
     * usefull during database seeding
     *
     * @return array
     */
    public function filesystemsImages()
    {
        $path = $this->getFullPath();
        $images = [];
        if (is_dir($path)) {
            $images = array_merge($images, glob($path . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE));
            foreach (scandir($path) as $contentPath) {
                if (is_dir($path . '/' . $contentPath) && $contentPath !== '.' && $contentPath !== '..') {
                    $images = array_merge($images, glob($path . '/' . $contentPath . '/*.{jpg,jpeg,png,gif}', GLOB_BRACE));
                }
            }
        }
        return $images;
    }

    /**
     * return content within partner in the name
     * @return [ManuscriptContentImage]
     *
     */
    public function contentPartners()
    {
        return (new ManuscriptContentImage())
            ->find(
                ['manuscript_id=? AND name like ?', $this->id, '%partner%'],
            );
    }

    /**
     * createPartner
     *
     * @param  mixed $newBase64DecodeContent
     * @param  mixed $url
     * @return ManuscriptContentImage
     */
    public function createPartner(string $newBase64EncodeContent = null, string $url = null)
    {
        $extension = '';
        if ($newBase64EncodeContent) {
            $fileTmpName = tmpfile();
            fwrite($fileTmpName, base64_decode($newBase64EncodeContent));
            $mimeContentType = mime_content_type($fileTmpName);
            fclose($fileTmpName);

            if ($mimeContentType == 'image/jpeg') {
                $extension = 'jpg';
            } elseif ($mimeContentType == 'image/png') {
                $extension = 'png';
            }
            $manuscriptContentAttributes = [
                'manuscript_id' => $this->id,
                'name' => 'partner-' . time() . '.' . $extension,
                'extension' => $extension,
                'url' => $url ? $url : ''
            ];
            $manuscripContentPartner = ManuscriptContentImage::store($manuscriptContentAttributes);
            $manuscripContentPartner->updateImage($newBase64EncodeContent);
        }
        return $manuscripContentPartner;
    }

    /**
     * return nakala url
     * metadata collection xml
     * ex: for GA 099 page 161 and page 162 https://api.nakala.fr/datas/10.34847/nkl.d3d1coro
     *
     * @return string
     */
    public function getNakalaUrl()
    {
        $firstContentMeta = $this->firstContentMeta();
        $firstContentMetaData = $firstContentMeta->getContent();
        if (isset($firstContentMetaData['data']['foaf'])) {
            $foaf_data = $firstContentMetaData['data']['foaf'];
            $nakala_page_url = json_decode(json_encode($foaf_data['Document'][0]))->{'@attributes'}->about;

            // Rewrite nakala page URL to add the missing part
            $nakala_page_url = str_replace('data', 'page/data', $nakala_page_url);

            // Return rewritten nakala URL from old manuscripts format
            return $nakala_page_url;
        }

        // Return nakala URL from new manuscripts format
        return $this->url;
    }

    /**
     * return nakala public url
     * metadata collection page
     * ex: for GA 099 page 161 and page 162 https://nakala.fr/10.34847/nkl.d3d1coro
     *
     * @return string
     */
    public function getNakalaPublicUrl()
    {
        // Get Nakala document URL from API
        $nakala_doc_url_from_api = $this->getNakalaUrl();

        // Rewrite base URL first
        $nakala_public_url = str_replace('api.', '', $nakala_doc_url_from_api);

        // Remove the extra '/datas' from the URL
        $nakala_public_url = str_replace('/datas', '', $nakala_public_url);

        // Return rewritten nakala URL from new manuscripts format or default URL
        return (isset($nakala_public_url) && !empty($nakala_public_url) ? $nakala_public_url : $nakala_doc_url_from_api);
    }

    /**
     * return all metas
     *
     * @return array
     */
    public function getAllMetas()
    {
        $return = [];
        // new Nakala Version
        if ($this->content) {
            $nakala = new Nakala();
            $nakala->set_url('https://www.fakeurl.com/fake');
            $nakala->set_raw_json(html_entity_decode($this->content));
            foreach ($nakala->convert_metas() as $meta) {
                $return = array_merge($return, $meta);
            }
            return $return;
        }

        // old xml format locally stored
        $rdf = new Rdf();
        $rdf->load('manuscripts/' . $this->name . '/' . $this->firstContentMeta()->name);
        $rdf->parse();
        $dcterms = $rdf->get_document()['dcterms'];
        foreach ($dcterms as $key => $value) {
            $return[$key] = $value[0];
        }
        return $return;
    }

    /**
     * return searched meta value
     *
     * @param string $metaName
     * @return string|bool
     */
    public function getMeta(string $metaName)
    {
        // new Nakala Version
        if ($this->content) {
            return Nakala::getMetaFromJson(html_entity_decode($this->content), $metaName);
        }

        // old xml format locally stored
        if (is_file($this->getFullPath() . '/' . $this->firstContentMeta()->name)) {
            $rdf = new Rdf();
            $rdf->load('manuscripts/' . $this->name . '/' . $this->firstContentMeta()->name);
            $rdf->parse();
            $dcterms = $rdf->get_document()['dcterms'];
            foreach ($dcterms as $key => $value) {
                switch ($key) {
                    case $metaName:
                        // Set label from XML data
                        return (string)$dcterms[$metaName][0];
                        break;
                }
            }
        }
    }

    /**
     * return array of value about searched meta
     *
     * @param string $metaName
     * @return array
     */
    public function getMetas(string $metaName)
    {
        // new Nakala Version
        if ($this->content) {
            return Nakala::getMetasFromJson(html_entity_decode($this->content), $metaName);
        }

        // old xml format locally stored
        if (is_file($this->getFullPath() . '/' . $this->firstContentMeta()->name)) {
            $rdf = new Rdf();
            $rdf->load('manuscripts/' . $this->name . '/' . $this->firstContentMeta()->name);
            $rdf->parse();
            $dcterms = $rdf->get_document()['dcterms'];
            $return = [];
            foreach ($dcterms as $key => $value) {
                switch ($key) {
                    case $metaName:
                        // Set label from XML data
                        $return[] = (string)$dcterms[$metaName][0];
                }
            }
            return $return;
        }
        return [];
    }

    /**
     * return array of fullpath images related to manuscript
     *
     * @return array
     */
    public function getImages(): array
    {
        $return = [];
        $manuscriptFullPath = $this->getFullPath();
        foreach ($this->contentsImage() as $manuscriptContent) {
            $return[] = $manuscriptFullPath . '/' . $manuscriptContent->name;
        }
        return $return;
    }

    /**
     * return manifest of manuscript
     *
     * @return stdClass
     */
    public function getManifest($iiifVersion): stdClass
    {
        // Generate item object
        $manifest = new stdClass();
        $manifest->{'@id'} = $this->f3->get('SCHEME') . '://' . $this->f3->get('SERVER.HTTP_HOST') . $this->f3->get('MR_PATH_WEB');
        $manifest->{'@id'} .= 'api/iiif/' . $iiifVersion . '/' . $this->name . '/manifest';
        $manifest->{'@type'} = 'sc:Manifest';

        $manifest->label = $this->getDisplayname();
        return $manifest;
    }

    /**
     * return encoded name
     *
     * @return string
     */
    public function getEncodedId(): string
    {
        return base64_encode($this->name);
    }

    /**
     * return partner url
     *
     * @return array|null
     */
    public function getPartnerUrl()
    {
        // Partner list
        $known_partners = [
            ['id' => 30001, 'url' => 'https://www.unibas.ch/en/University/University-Society/University-Library-Basel-.html'],
            ['id' => 20003, 'url' => 'https://digi.vatlib.it/'],
            ['id' => 30304, 'url' => 'https://catalogue.bnf.fr/'],
            ['id' => 601393, 'url' => 'http://www.bible-orient-museum.ch/'],
        ];

        // Manuscript partner URL definition
        foreach ($known_partners as $partner) {
            // JSON config never loaded here, no the test below will fail and raise an error on PHP 8.1.x
            // Needs to disable the test until further debugging
            // if ($this->f3->app_config->debug === true) {
            //     echo '<!-- Searching static partner [' . $partner['id'] . '] / Manuscript partner [' . $this->getMeta('dcterm-temporal') . '] -->' . PHP_EOL;
            // }

            if ((int)$partner['id'] === (int)$this->getMeta('dcterm-temporal')) {
                // Define new partner URL from list
                $manuscript_partner_url = $partner['url'];

                // JSON config never loaded here, no the test below will fail and raise an error on PHP 8.1.x
                // Needs to disable the test until further debugging
                // if ($this->f3->app_config->debug === true) {
                //     echo '<!-- Known partner URL: ' . $manuscript_partner_url . ' -->' . PHP_EOL;
                // }

                // Leave the loop once partner found
                break;
            }
        }

        // Manuscript partner URL extraction
        if (!isset($manuscript_partner_url)) {
            // Define new partner URL from manuscript
            $manuscript_partner_url = parse_url($this->getMeta('dcterm-isVersionOf'));

            // JSON config never loaded here, no the test below will fail and raise an error on PHP 8.1.x
            // Needs to disable the test until further debugging
            // if ($this->f3->app_config->debug === true) {
            //     echo '<!-- Extracted partner URL: ' . print_r($manuscript_partner_url, true) . ' -->' . PHP_EOL;
            // }
        }
        return $manuscript_partner_url;
    }

    /**
     *  return url of first content meta
     *  ex: GA 03: https://api.nakala.fr/data/11280%2F73240506/b98586cd8ff9cce1e2e54cd5cd46365aa362dcb2
     *
     * @return string
     */
    public function getAboutHref()
    {
        $firstContentMeta = $this->firstContentMeta();

        // old Rdf
        $firstContentMetaData = $firstContentMeta->getContent();
        if (isset($firstContentMetaData['data'])) {
            $rdf_data = $firstContentMetaData['data'];
            //$dcterms_data = $firstContentMetaData['dcterms'];
            $foaf_data = $rdf_data['foaf'];
            return json_decode(json_encode($foaf_data['Document'][0]))->{'@attributes'}->about;
        }

        return $firstContentMeta->url;
    }

    /**
     * Return Manuscript language extended
     * ex. Ancient Greek
     *
     * @return string
     */
    public function getLangExtended()
    {
        $metaLanguage = $this->getMeta('dcterm-language');

        if (isset($this->f3->get('MR_CONFIG')->languages->{$metaLanguage})) {
            return $this->f3->get('MR_CONFIG')->languages->{$metaLanguage}->name;
        }

        return $metaLanguage;
    }

    /**
     * Return Manuscript language code
     * ex. grc for Ancient Greek
     *
     * @return string
     */
    public function getLangCode()
    {
        $metaLanguage = $this->getMeta('dcterm-language');

        if (isset($this->f3->get('MR_CONFIG')->languages->{$metaLanguage})) {
            return $metaLanguage;
        }

        foreach ($this->f3->get('MR_CONFIG')->languages as $langCode => $langObj) {
            if ($langObj->name == $metaLanguage) {
                return $langCode;
            }
        }

        // not found in congig.json languages
        return null;
    }

    /**
     * Return Manuscript language font family to render correctly
     *
     * @return string
     */
    public function getLangFont()
    {
        $manuscriptLangCode = $this->getLangCode();
        foreach ($this->f3->get('MR_CONFIG')->languages as $langCode => $langObj) {
            if ($langCode == $manuscriptLangCode) {
                return $langObj->font;
            }
        }

        return 'xxx';
    }

    /**
     * allPublished
     *
     * @param  bool $published
     * @return [static]
     */
    public static function allPublished(bool $published = true)
    {
        $f3 = Base::instance();

        if ($f3->get('pageno')) {
            $f3->set(
                'perpage',
                $f3->get('GET.perpage') ? $f3->get('GET.perpage') : 10
            );

            $offset = ($f3->get('pageno') - 1) * $f3->get('perpage');

            $f3->set('total_rows', count((new static())->find(['published=?', $published ? 1 : 0])));
            $f3->set('total_pages', ceil($f3->get('total_rows') / $f3->get('perpage')));

            $f3->set('prevPage', $f3->get('pageno') > 1 ? '?pageno=' . ($f3->get('pageno') - 1) : null);
            $f3->set('nextPage', $f3->get('pageno') < $f3->get('total_pages') ? '?pageno=' . ($f3->get('pageno') + 1) : null);
            return (new static())
                ->find(
                    ['published=?', $published ? 1 : 0],
                    [
                        'order' => 'temporal ASC',
                        'offset' => $offset,
                        'limit' => $f3->get('perpage')
                    ]
                );
        }

        return (new static())
            ->find(
                ['published=?', $published ? 1 : 0],
                ['order' => 'temporal ASC']
            );
    }

    /**
     * seed Images From Filesystems
     * Partner image
     * Folio images: if named _original then copyrighted one will be generated automatically     *
     *
     * @return array
     */
    public function seedImagesFromFilesystems(): array
    {
        $contentsSeeded = [];

        // empty ContentPartner from db
        foreach ($this->contentPartners() as $contentPartner) {
            $contentPartner->erase();
        }

        // empty ContentImage from db and
        foreach ($this->contentsImage() as $contentImage) {
            $contentImage->erase();
        }

        // delete copyrighted image files if its _original is present
        foreach ($this->filesystemsImages() as $fileImage) {
            if (strpos($fileImage, '_original') === false && is_file(substr($fileImage, 0, -4) . '_original' . substr($fileImage, -4))) {
                unlink($fileImage);
            }
        }

        // loop inside images in filesystems to import in db
        $manuscriptFullPath = $this->getFullPath();

        foreach ($this->filesystemsImages() as $fileImage) {
            $url = '';
            $content = '';
            $name = str_replace($manuscriptFullPath . '/', '', $fileImage);

            // if image is _original then name='' and save with _original in content in json
            if (strpos($name, '_original') !== false) {
                $arrFilename = explode('/', $name);
                $privacyFile = $manuscriptFullPath . '/' . $arrFilename[0] . '/copyright.txt';
                $content = [
                    'name' => $name,
                    'copyright' => is_file($privacyFile) ? file_get_contents($privacyFile) : ''
                ];
                $name = '';
            } else {
                $content = [
                    'copyright' => '',
                    'md5' => md5_file($fileImage)
                ];
            }

            // if is partner retrieve its url
            if (strpos($fileImage, 'partner') !== false) {
                $filePartner = $manuscriptFullPath
                    . '/' . pathinfo($fileImage)['filename'] . '-partner.txt';
                if (is_file($filePartner)) {
                    return file_get_contents($filePartner);
                }

                $getPartnerUrl = $this->getPartnerUrl();
                $url = is_array($getPartnerUrl)
                    ? $getPartnerUrl['scheme'] . '://' . $getPartnerUrl['host'] . $getPartnerUrl['path']
                    : $getPartnerUrl;
            }

            // save in db
            $manuscriptContentAttributes = [
                'manuscript_id' => $this->id,
                'name' => $name,
                'extension' => pathinfo($fileImage)['extension'],
                'url' => $url,
                'content' => json_encode($content),
            ];
            $contentsSeeded[] = (new ManuscriptContentSeeder())->save($this, $manuscriptContentAttributes);
        }
        return $contentsSeeded;
    }

    /**
     * getFullPath
     * return manuscript fullpath
     * @return string
     */
    public function getFullPath()
    {
        return $this->f3->get('MR_DATA_PATH') . '/' . $this->name;
    }

    /**
     * getDisplayname
     * from getMeta('dcterm-bibliographicCitation')
     * @return string
     */
    public function getDisplayname()
    {
        return $this->getMeta('dcterm-bibliographicCitation');
    }


    /**
     * clean orphan contentsImage and files
     *
     * @return array
     */
    public function clean(): array
    {
        $return = [
            'contentImages' => []
        ];
        $folioNames = [];
        foreach ($this->contentsFolios() as $contentsFolio) {
            $folioNames[] = $contentsFolio->getFolioName();
        }
        $contentImagesAssociatedWithFolio = [];
        foreach ($this->contentsImage() as $contentImages) {
            if (!in_array($contentImages->getFolioName(), $folioNames)) {
                $return['contentImages'][] = $contentImages->getImagePath();
                $contentImages->remove();
                // it is in folio but duplicated row
            } else if (in_array($contentImages->getFolioName(), $contentImagesAssociatedWithFolio)) {
                $return['contentImages'][] = $contentImages->getImagePath() .  " [keeping files as duplicated record]";
                $contentImages->remove(false);
            } else {
                $contentImagesAssociatedWithFolio[] = $contentImages->getFolioName();
            }
        }

        // FS
        $imagesAssociatedToFolios = [];
        foreach ($this->contentsFolios() as $contentsFolio) {
            $folioImage = $contentsFolio->getFolioImage();
            if ($folioImage) {
                $imagesAssociatedToFolios[] = $folioImage->getImagePath(true);
                $imagesAssociatedToFolios[] = $folioImage->getImagePath();
            }
            // $contentsFolio()
        }
        foreach ($this->contentPartners() as $contentPartner) {
            $imagesAssociatedToFolios[] = $contentPartner->getImagePath(true);
            $imagesAssociatedToFolios[] = $contentPartner->getImagePath();
        }
        $orphanFiles = array_diff(
            $this->filesystemsImages(),
            $imagesAssociatedToFolios
        );
        $return = array_merge($return, [
            'imagesAssociatedToFolios' => $imagesAssociatedToFolios,
            'filesystemsImages' => $this->filesystemsImages(),
            'orphanFiles' => $orphanFiles
        ]);
        foreach ($orphanFiles as $orphanFile) {
            unlink($orphanFile);
        }

        return $return;
    }
}
