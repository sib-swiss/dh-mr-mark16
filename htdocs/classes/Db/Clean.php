<?php

namespace classes\Db;

use Base;
use classes\Models\Manuscript;
use Log;

/**
 * clean db from orphan contens: image not associate to folios
 * and its corresponding files
 *
 * @author Silvano Aldà / SIB - 2021
 */
class Clean
{
    public function __construct()
    {
        $this->f3 = Base::instance();
        $this->logger = new Log(str_replace('\\', '-', __CLASS__) . '.log');
    }

    /**
     * handle the cleaning
     *
     * @return array
     */
    public function handle():array
    {
        $deleted = [];
        /**
         *  temporary return Todo:
         */

        return ['ToDo'];

        foreach (Manuscript::all() as $manuscript) {
            $manuscriptFoliosImagesIds = [];
            foreach ($manuscript->contentsFolios() as $contentsFolio) {
                $folioImage = $contentsFolio->getFolioImage();
                if ($folioImage) {
                    $manuscriptFoliosImagesIds[] = $folioImage->id;
                }
            }
            foreach ($manuscript->contentsImage() as $contentImage) {
                if (!in_array($contentImage->id, $manuscriptFoliosImagesIds)) {
                    $this->logger->write('delete: ' . $contentImage->name . ': ' . $contentImage->getFolioName());
                    //$contentImage->erase();
                    $deleted[] = $contentImage->name . ': ' . $contentImage->getFolioName();
                }
            }
        }
        return $deleted;
    }
}
