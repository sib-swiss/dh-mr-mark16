<?php

namespace classes\Db;

use classes\Models\ManuscriptContent;
use classes\Models\ManuscriptContentImage;
use Log;

/**
 * Save content in db while seeding
 *
 * @author Silvano Aldà / SIB - 2021
 */
class ManuscriptContentSeeder
{
    private $logger;

    /**
     * Contructor
     *
     * @param string $nakalaCollectionURL
     */
    public function __construct()
    {
        $this->logger = new Log(str_replace('\\', '-', __CLASS__) . '.log');
    }

    public function save($manuscript, $manuscriptContentAttributes)
    {
        $manuscriptContent = null;
        if ($manuscriptContentAttributes['name']) {
            $manuscriptContent = (new ManuscriptContent())
            ->findone(
                [
                    'manuscript_id=? 
                    AND name=? ',
                    $manuscript->id,
                    $manuscriptContentAttributes['name']
                ]
            );
        } else {
            $contentDecoded = json_decode($manuscriptContentAttributes['content']);
            $manuscriptContent = (new ManuscriptContent())
            ->findone(
                [
                    'manuscript_id=? 
                    AND content like ? ',

                    $manuscript->id,
                    '%"name":"' . $contentDecoded->name . '%'
                ]
            );
        }

        if ($manuscriptContent) {
            $this->logger->write('seedContent.update: ' . json_encode($manuscriptContentAttributes));
            $manuscriptContent->copyfrom($manuscriptContentAttributes);
            $manuscriptContent->update();
        } else {
            $this->logger->write('seedContent.store: ' . json_encode($manuscriptContentAttributes));
            $manuscriptContent = ManuscriptContent::store($manuscriptContentAttributes);
        }

        // image original
        if ($manuscriptContent->name == '') {
            (new ManuscriptContentImage())->findBy('id', $manuscriptContent->id)->updateFromOriginal();
        }

        return $manuscriptContent;
    }
}
