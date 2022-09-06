<?php

namespace classes\Models;

use Base;
use DB\SQL\Mapper;
use stdClass;

/**
 * This class is responsible of managing manuscript content properties and method
 * Dependedencies: Fat free framework
 * @author Silvano Aldà / SIB - 2021
 */
class ManuscriptContent extends Mapper
{
    use ModelTrait;
    protected $attributes = [
        'id',
        'manuscript_id',
        'name',
        'extension',
        'url',
        'content'
    ];

    protected $f3;
    protected $manuscript;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->f3 = Base::instance();
        parent::__construct($this->f3->get('DB'), 'manuscript_contents');
    }

    /**
     * return it's relative Manuscript or false
     *
     * @return Manuscript|bool
     */
    public function manuscript()
    {
        if ($this->manuscript) {
            return $this->manuscript;
        }
        $this->manuscript = Manuscript::findBy('id', $this->manuscript_id);
        return $this->manuscript;
    }

    /**
     * return base54encoded name
     *
     * @return string
     */
    public function getEncodedId()
    {
        return base64_encode(end(explode('/', $this->name)));
    }

    /**
     * return Folio name
     *
     * @return string
     */
    public function getFolioName()
    {
        $name = str_replace('_Metadata', '', $this->name);
        $parts1 = explode('/', $name);
        if (count($parts1) == 1) {
            $parts2 = explode('.', $parts1[0]);
            return  substr($name, 0, -strlen(end($parts2)) - 1);
        }

        //  GA304_240r/GA304_240r_ENG.html
        $parts2 = explode('_', $parts1[1]);
        $parts3 = explode('_', $parts2[1]);
        $parts4 = explode('.', $parts3[0]);
        return $parts2[0] . '_' . $parts4[0];
    }

    /**
     * updateContent
     *
     * @param  mixed $content
     * @return void
     */
    public function updateContent($content)
    {
        if (is_array($content)) {
            $contentDecoded = $this->content ? json_decode($this->content) : [];
            if (!$contentDecoded) {
                $contentDecoded = new stdClass();
            }
            foreach ($content as $k => $v) {
                if ($v) {
                    $contentDecoded->$k = $v;
                } elseif (isset($contentDecoded->$k)) {
                    unset($contentDecoded->$k);
                }
            }
            $this->content = json_encode($contentDecoded);
            $this->save();
            return;
        }

        $this->content = $content;
        $this->save();
    }
}
