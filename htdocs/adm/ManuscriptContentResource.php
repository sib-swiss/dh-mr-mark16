<?php

namespace adm;

use classes\BaseResource;
use classes\Models\Manuscript;
use classes\Models\ManuscriptContentImage;
use classes\Models\ManuscriptContentMeta;
use stdClass;

class ManuscriptContentResource extends BaseResource
{

    public function __construct()
    {
        parent::__construct();
        $this->f3->get('authcheck')();
    }

    public function get()
    {
        $this->f3->error(405);
    }

    public function post()
    {
        // Backend response selector
        if (count($this->f3->get('POST')) > 0) {
            // Handle manuscript folios related content
            if (array_key_exists('manuscript_folio_id', $this->f3->get('POST'))) {
                return $this->folioImageUpdate();
            }

            // Handle manuscript partners related content
            if (array_key_exists('manuscript_partner_id', $this->f3->get('POST'))) {
                return $this->partnerUpdate();
            }

            // Handle manuscript related content
            if (array_key_exists('manuscript_id', $this->f3->get('POST'))) {
                return $this->manuscriptUpdate();
            }
        }

        // Handle unmanaged content
        $response = new stdClass();
        $response->success = false;
        $response->message = 'Backend handler not found.';
        return $this->returnResponse($response);
    }

    /**
     * manuscriptUpdate
     *
     * @return void
     */
    private function manuscriptUpdate()
    {
        $response = new stdClass();
        $manuscript = Manuscript::findByEncodedId($this->f3->get('POST.manuscript_id'));
        if (!$manuscript) {
            $response->success = false;
            $response->message = 'Manuscript not found for id ' . $this->f3->get('POST.manuscript_id');
            return $this->returnResponse($response);
        }

        // Manuscript -- Partner -- New
        if (array_key_exists('manuscript_add_partner', $this->f3->get('POST')) && (bool)$this->f3->get('POST.manuscript_add_partner') === true) {
            return $this->partnerAdd();
        }

        // Manuscript -- Published -- Status update
        if (array_key_exists('manuscript_published', $this->f3->get('POST'))) {
            $newPublished = $this->f3->get('POST.manuscript_published');
            $response->debug['actions'][] = 'published: ' . $manuscript->published . ' TO ' . $newPublished;
            $manuscript->published = $newPublished;
        }

        $manuscript->save();
        $response->success = true;
        $response->published = ((bool)$newPublished === true ? true : false);
        $response->message = 'Manuscript updated succesfully';
        return $this->returnResponse($response);
    }

    /**
     * folioImageUpdate
     *
     * @return void
     */
    private function folioImageUpdate()
    {
        $response = new stdClass();

        // retrieve folio
        $id = $this->f3->get('POST.manuscript_folio_id');
        $manuscriptContentMeta = ManuscriptContentMeta::findBy('id', $id);

        // if folio not found return error
        if (!$manuscriptContentMeta) {
            $response->success = false;
            $response->message = 'ManuscriptContentMeta not found for id ' . $id;
            return $this->returnResponse($response);
        }

        // get information about new image content and if original
        $original = $this->f3->get('POST.manuscript_folio_image_type') == 'original' ? true : false;
        $newBase64DecodeContent = $this->f3->get('POST.manuscript_folio_image_content')
            ? base64_decode($this->f3->get('POST.manuscript_folio_image_content'))
            : null;
        if ($newBase64DecodeContent) {
            $fileTmpName = tmpfile();
            fwrite($fileTmpName, $newBase64DecodeContent);
            $mimeContentType = mime_content_type($fileTmpName);
            fclose($fileTmpName);

            // mime not valid: return error
            if ($mimeContentType !== 'image/jpeg') {
                $response->success = false;
                $response->message = 'only jpg are valid, you uploaded: ' . $mimeContentType;
                return $this->returnResponse($response);
            }
        }

        // update/create contentImage
        $manuscripContentImage = $manuscriptContentMeta->getFolioImage();
        if (!$manuscripContentImage) {
            // content image not present, if not upload image return error
            if (!$newBase64DecodeContent) {
                $response->success = false;
                $response->message = 'uploaded image as it is not present yet for this folio';
                return $this->returnResponse($response);
            }

            // content image not present: store new one
            $manuscriptContentAttributes = [
                'manuscript_id' => $manuscriptContentMeta->manuscript_id,
                'name' => $manuscriptContentMeta->getFolioName() . '.jpg',
                'extension' => 'jpg',
            ];
            $manuscripContentImage = ManuscriptContentImage::store($manuscriptContentAttributes);
            $response->debug['actions'][] = 'Stored New ManuscriptContentImage: ' . json_encode($manuscriptContentAttributes);
        }

        // update image content and its copyright
        if ($newBase64DecodeContent) {
            $manuscripContentImage->updateImage(base64_encode($newBase64DecodeContent), $original);
            $response->debug['actions'][] = 'updateImage: original: ' . ($original ? 'yes' : 'no');
        }

        // update copyright
        if ($this->f3->get('POST.manuscript_folio_image_copyright') && $this->f3->get('POST.manuscript_folio_image_copyright') !== $manuscripContentImage->getCopyrightText()) {
            // check if there is original
            if (!$manuscripContentImage->originalExists()) {
                $response->debug['actions'][] = 'orignal not exist: ' . $manuscripContentImage->getImagePath(true);
                $response->success = false;
                $response->message = 'Cannot update copyright text as the original image doesn\'t exist';
                return $this->returnResponse($response);
            }
            $response->debug['actions'][] = 'update copyright: FROM ' . $manuscripContentImage->getCopyrightText() . ' TO ' . $this->f3->get('POST.manuscript_folio_image_copyright');
            $manuscripContentImage->updateContent([
                'copyright' => $this->f3->get('POST.manuscript_folio_image_copyright')
            ]);
            $response->debug['actions'][] = 'updated copyright: ' . $manuscripContentImage->getCopyrightText();
        }

        // update copyright
        if ($this->f3->get('POST.manuscript_folio_image_copyright_fontsize') && $this->f3->get('POST.manuscript_folio_image_copyright_fontsize') !== $manuscripContentImage->getCopyrightFontSize()) {

            $response->debug['actions'][] = 'update copyright FontSize: FROM ' . $manuscripContentImage->getCopyrightFontSize() . ' TO ' . $this->f3->get('POST.manuscript_folio_image_copyright_fontsize');
            $manuscripContentImage->updateContent([
                'fontsize'=> $this->f3->get('POST.manuscript_folio_image_copyright_fontsize')
            ]);
            $response->debug['actions'][] = 'updated copyright fontSize: ' . $manuscripContentImage->getCopyrightFontSize();
        }
        

        $manuscripContentImage->updateFromOriginal();
        $response->debug['actions'][] = 'updateFromOriginal';

        // return success
        $response->success = true;
        $response->message = 'Updated succesfully';
        if ($this->f3->get('QUIET') !== true) {
            $response->fileContent = $manuscripContentImage->imageContent();
        }
        return $this->returnResponse($response);
    }

    /**
     * partner Add
     *
     * @return void
     */
    private function partnerAdd()
    {
        $response = new stdClass();

        // retrieve manuscript
        $manuscript = Manuscript::findByEncodedId($this->f3->get('POST.manuscript_id'));
        $response->debug['actions'][] = 'add-partner: TO ' . $manuscript->name;

        // add PArtner
        $newPartner = $manuscript->createPartner(
            $this->f3->get('POST.manuscript_partner_image_content'),
            $this->f3->get('POST.manuscript_partner_url'),
        );

        // response
        $response->debug['actions'][] = 'Stored New Partner: ' . json_encode($newPartner);
        $response->fileContent = $newPartner->imageContent();
        $response->message = 'Partner Added successfully';
        $response->success = true;
        return $this->returnResponse($response);
    }

    /**
     * partnerUpdate
     *
     * @return void
     */
    private function partnerUpdate()
    {
        $response = new stdClass();
        // retrieve partner
        $id = $this->f3->get('POST.manuscript_partner_id');
        $manuscripContentParnter = ManuscriptContentImage::findBy('id', $id);

        $newUrl = $this->f3->get('POST.manuscript_partner_url');
        if ($newUrl !== $manuscripContentParnter->url) {
            $response->success = true;
            $response->debug['actions'][] = $manuscripContentParnter->url . '   TO   ' . $newUrl;
            $manuscripContentParnter->url = $newUrl;
            $manuscripContentParnter->save();
        }

        $newPartnerBase64DecodeContent = $this->f3->get('POST.manuscript_partner_image_content')
            ? base64_decode($this->f3->get('POST.manuscript_partner_image_content'))
            : null;
        if ($newPartnerBase64DecodeContent) {
            $response->success = false;
            $fileTmpName = tmpfile();
            fwrite($fileTmpName, $newPartnerBase64DecodeContent);
            $mimeContentType = mime_content_type($fileTmpName);
            fclose($fileTmpName);

            // mime not valid: return error
            if ($mimeContentType !== 'image/jpeg' && $mimeContentType !== 'image/png') {
                $response->success = false;
                $response->message = 'only jpg and ong are valid, you uploaded: ' . $mimeContentType;
                return $this->returnResponse($response);
            }
            // update image content and its copyright
            if ($newPartnerBase64DecodeContent) {
                $manuscripContentParnter->updateImage(base64_encode($newPartnerBase64DecodeContent));
                $response->debug['actions'][] = 'Partner.updateImage';
            }
            $response->success = true;
        }

        // test reponse
        $response->fileContent = $manuscripContentParnter->imageContent();
        $response->message = 'Partner updated successfully';
        return $this->returnResponse($response);
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
