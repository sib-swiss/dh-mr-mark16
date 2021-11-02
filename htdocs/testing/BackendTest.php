<?php

namespace Tests;

require __DIR__ . '/../vendor/autoload.php';

use classes\Models\Manuscript;
use classes\Models\ManuscriptContentImage;
use classes\Models\ManuscriptContentMeta;
use Test;

class BackendTest extends TestCase
{
    private $manuscriptContent;
    private $postData;

    public function setup()
    {
        $this->manuscriptContent = ManuscriptContentMeta::findBy('extension', 'xml');
        $this->postData = [
            'manuscript_folio_id' => $this->manuscriptContent->id,
            'manuscript_folio_image_type' => '',
            'manuscript_folio_image_copyright' => 'Copyright Test: ' . date('d.m.Y H:i:s'),
            'position' => ''
        ];
        $this->test = new Test();
        include(__DIR__ . '/../inc/config.php');

        $_SERVER['PHP_AUTH_USER'] = $app_config->admin->username;
        $_SERVER['PHP_AUTH_PW'] =  $app_config->admin->password;
    }

    /**
     * testUpdateCopyright
     *
     * @return void
     */
    public function testUpdateCopyrightWithoutOriginal()
    {
        $this->setup();

        $manuscriptContentFolioImage = $this->manuscriptContent->getFolioImage();
        $originalImageFile = $manuscriptContentFolioImage->getImagePath(true);
        if (is_file($originalImageFile)) {
            unlink($originalImageFile);
        }

        $originalDetails = $manuscriptContentFolioImage->details(true);
        $this->test->expect(
            substr($originalDetails, 0, strlen('No file: ')) == 'No file: ',
            'Original File should not be founded'
        );
        $this->f3->mock(
            'POST /admin/edit/' . $this->manuscriptContent->id,
            $this->postData
        );
        $response = json_decode($this->f3->get('response'));

        //  What should be the reponse??
        //  some erro message!
        $this->test->expect(
            $response->success == false,
            'success should be false'
        );
        $this->test->expect(
            $response->message == "Cannot update copyright text as the original image doesn't exist",
            'message not corresponding: ' . $response->message
        );

        return $this->test;
    }

    /**
     * testUpdateCopyright
     *
     * @return void
     */
    public function testUpdateCopyrightWithOriginal()
    {
        $this->setup();

        $this->manuscriptContent = ManuscriptContentMeta::findBy('name', 'sa9_f.59v.xml');
        $this->postData['manuscript_folio_id'] = $this->manuscriptContent->id;

        $this->f3->mock(
            'POST /admin/edit/' . $this->manuscriptContent->id,
            $this->postData
        );
        $response = json_decode($this->f3->get('response'));
        $manuscripContentImage = $this->manuscriptContent->getFolioImage();

        $this->test->expect(
            $manuscripContentImage->getCopyrightText() == $this->postData['manuscript_folio_image_copyright'],
            $manuscripContentImage->name . " copyright: expected '" . $this->postData['manuscript_folio_image_copyright'] . "', got: '" . $manuscripContentImage->getCopyrightText() . "'"
        );

        return $this->test;
    }

    public function testUpdateImageContentOriginal()
    {
        $this->setup();

        $getFolioImageBefore = $this->manuscriptContent->getFolioImage();
        $getFolioImageBeforeDetails = $getFolioImageBefore->details(true);

        $file = __DIR__ . '/data/GA01_f.227v.jpg';
        $fileSize = getimagesize($file);
        $originalWidth = 4486;
        $originalHeight = 5026;
        $this->postData['manuscript_folio_image_type'] = 'original';
        $this->postData['manuscript_folio_image_content'] = base64_encode(file_get_contents($file));

        $this->f3->mock(
            'POST /admin/edit/' . $this->manuscriptContent->id,
            $this->postData
        );
        $response = json_decode($this->f3->get('response'));

        $getFolioImageAfter = $this->manuscriptContent->getFolioImage();
        $getFolioImageAfterDetails = $getFolioImageAfter->details(true);

        $this->test->expect(
            1886 == $getFolioImageAfterDetails['size'][0],
            'widht not right'
        );

        $this->test->expect(
            2113 == $getFolioImageAfterDetails['size'][1],
            'height not right'
        );

        $this->test->expect(
            $response->success,
            $response->message
        );

        return $this->test;
    }

    public function testUpdateImageContentNotOriginalAndCopyright()
    {
        $this->setup();

        $file = $this->f3->get('MR_DATA_PATH') . '/SA14L/sa14L_f.5v/sa14L_f.5v.jpg';
        $this->postData['manuscript_folio_image_type'] = '';
        $this->postData['manuscript_folio_image_content'] = base64_encode(file_get_contents($file));

        $this->f3->mock(
            'POST /admin/edit/' . $this->manuscriptContent->id,
            $this->postData
        );
        $response = json_decode($this->f3->get('response'));

        $this->test->expect(
            $response->success == false,
            $response->message
        );

        $this->test->expect(
            $response->message == "Cannot update copyright text as the original image doesn't exist",
            'Message not corresponding'
        );

        return $this->test;
    }

    public function testUpdateImageContentNotOriginalWithoutCopyright()
    {
        $this->setup();

        $file = $this->f3->get('MR_DATA_PATH') . '/SA14L/sa14L_f.5v/sa14L_f.5v.jpg';
        $this->postData['manuscript_folio_image_type'] = '';
        $this->postData['manuscript_folio_image_content'] = base64_encode(file_get_contents($file));
        $this->postData['manuscript_folio_image_copyright'] = '';

        $this->f3->mock(
            'POST /admin/edit/' . $this->manuscriptContent->id,
            $this->postData
        );
        $response = json_decode($this->f3->get('response'));

        $this->test->expect(
            $response->success,
            $response->message
        );

        return $this->test;
    }

    public function testUpdatePartnerUrl()
    {
        $this->manuscriptContent = ManuscriptContentImage::findWhere('name', 'like', '%partner%');
        $this->postData = [
            'manuscript_partner_id' => $this->manuscriptContent->id,
            'manuscript_partner_image_content' => null,
            'manuscript_partner_image_metas' => null,
            'manuscript_partner_url' => 'http://google.com?' . microtime()
        ];

        $this->f3->mock(
            'POST /admin/edit/' . $this->manuscriptContent->id,
            $this->postData
        );
        $response = json_decode($this->f3->get('response'));

        $this->test->expect(
            $response->success === true,
            'response success not true'
        );
        $updatedManuscriptContent = ManuscriptContentImage::findBy('id', $this->manuscriptContent->id);
        $this->test->expect(
            $updatedManuscriptContent->url == $this->postData['manuscript_partner_url'],
            'new url not corresponding: ' . $updatedManuscriptContent->url
        );

        return $this->test;
    }

    public function testUpdatePartnerImage()
    {
        $file = __DIR__ . '/data/partner-SCMS.jpg';
        $this->manuscriptContent = ManuscriptContentImage::findWhere('name', 'like', '%partner%');
        file_put_contents($this->manuscriptContent->getImagePath(), '');
        $this->test->expect(
            //md5($updatedManuscriptContent->imageContent())
            file_get_contents($this->manuscriptContent->getImagePath()) == '',
            'fileContent not empty'
        );
        $this->postData = [
            'manuscript_partner_id' => $this->manuscriptContent->id,
            'manuscript_partner_image_content' => base64_encode(file_get_contents($file)),
            'manuscript_partner_image_metas' => null,
            'manuscript_partner_url' => 'http://google.com?' . microtime()
        ];

        $this->f3->mock(
            'POST /admin/edit/' . $this->manuscriptContent->id,
            $this->postData
        );
        $response = json_decode($this->f3->get('response'));

        $this->test = new Test();
        $this->test->expect(
            $response->success === true,
            'response success not true: ' . json_encode($response)
        );

        $updatedManuscriptContent = ManuscriptContentImage::findBy('id', $this->manuscriptContent->id);
        $this->test->expect(
            $updatedManuscriptContent->imageContent() == $this->postData['manuscript_partner_image_content'],
            'fileContent not corresponding: ' . $this->manuscriptContent->getImagePath()
        );

        return $this->test;
    }

    public function testAddPartnerImageAndUrl()
    {
        $this->test = new Test();

        $file = __DIR__ . '/data/partner-SCMS.jpg';
        $manuscript = Manuscript::findBy('name', 'GA1230');

        $postData = [
            'manuscript_id' => $manuscript->getEncodedId(),
            'manuscript_add_partner' => 'true',
            'manuscript_partner_image_content' => base64_encode(file_get_contents($file)),
            'manuscript_partner_url' => 'http://google.com?' . microtime()
        ];

        $this->f3->mock(
            'POST /admin/edit/' . $manuscript->id,
            $postData
        );
        $response = json_decode($this->f3->get('response'));

        $this->test->expect(
            $response->success === true,
            'response success not true'
        );

        $ManuscriptNewPartner = ManuscriptContentImage::findBy('url', $postData['manuscript_partner_url']);
        $this->test->expect(
            $ManuscriptNewPartner !== null,
            'fileContent not corresponding: ' . $ManuscriptNewPartner->getImagePath()
        );

        $this->test->expect(
            $ManuscriptNewPartner->imageContent() == $postData['manuscript_partner_image_content'],
            'fileContent not corresponding: ' . $ManuscriptNewPartner->getImagePath()
        );

        $ManuscriptNewPartner->remove();

        $this->test->expect(
            !file_exists($ManuscriptNewPartner->getImagePath(true)),
            'original still exists'
        );

        $this->test->expect(
            !file_exists($ManuscriptNewPartner->getImagePath()),
            'copyrighted still exists'
        );

        $ManuscriptNewPartnerDeleted = ManuscriptContentImage::findBy('id', $ManuscriptNewPartner->id);
        $this->test->expect(
            $ManuscriptNewPartnerDeleted == null,
            'ManuscriptNewPartnerDeleted not deleted:'
        );

        return $this->test;
    }
}
