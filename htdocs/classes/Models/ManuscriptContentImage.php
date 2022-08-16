<?php

namespace classes\Models;

use Log;

/**
 * ManuscriptContentImage
 * Dependedencies: Fat free framework
 * @author Silvano Aldà / SIB - 2021
 */
class ManuscriptContentImage extends ManuscriptContent
{
    /**
     * get fullpath of image
     * if not name then return _original fullpath
     *
     * @return string
     */
    private function getFullPath(bool $original = false): string
    {
        // return the name, ex:
        // $this->f3->get('MR_DATA_PATH'). /GA1210/GA1210_f.104v/GA1210_f.104v.jpg
        $manuscriptFullPath = $this->manuscript()->getFullPath();
        if (!$original) {
            if ($this->name) {
                return $manuscriptFullPath . '/' . $this->name;
            }
            $contentDecoded = json_decode($this->content);
            return $manuscriptFullPath . '/' . str_replace('_original', '', $contentDecoded->name);
        }

        // return the original name, ex:
        // $this->f3->get('MR_DATA_PATH'). /GA1210/GA1210_f.104v/GA1210_f.104v_original.jpg
        $name = $this->name;
        if ($name) {
            $name = substr($name, 0, -strlen($this->extension) - 1) . '_original.' . $this->extension;
        } else {
            $contentDecoded = json_decode($this->content);

            if ($contentDecoded) {
                $name = $contentDecoded->name;
            }
        }

        return $manuscriptFullPath . '/' . $name;
    }

    /**
     * return fullpath of image
     *
     * @return string
     */
    public function getImagePath(bool $original = false)
    {
        return $this->getFullPath($original);
    }

    /**
     * return content type in mime format
     *
     * @return string
     */
    public function imageType()
    {
        return mime_content_type($this->getImagePath());
    }

    /**
     * return base64Encoded image
     *
     * @return string
     */
    public function imageContent(bool $original = false)
    {
        $path = $this->getImagePath($original);
        if (is_file($path)) {
            return base64_encode(file_get_contents($path));
        }
        return;
    }

    /**
     * overwrite image content
     *
     * @return string
     */
    public function updateImage(string $newBase64EncodeContent, bool $original = false)
    {
        $manuscriptFullPath = $this->manuscript()->getFullPath();
        if (!is_dir($manuscriptFullPath)) {
            mkdir($manuscriptFullPath);
        }
        if ($original) {
            file_put_contents($this->getImagePath($original), base64_decode($newBase64EncodeContent));

            $this->updateFromOriginal();
            return true;
        }
        file_put_contents($this->getImagePath(), base64_decode($newBase64EncodeContent));
        if ($this->originalExists()) {
            unlink($this->getImagePath(true));
        }
        return true;
    }

    /**
     * return array with image infos
     *
     * @return array
     */
    public function details(bool $original = false)
    {
        $imagePath = $this->getImagePath($original);

        if (!is_file($imagePath)) {
            return 'No file: ' . $imagePath;
        }
        $manuscript_image_mime_id = exif_imagetype($imagePath);
        return [
            'info' => pathinfo($imagePath),
            'mime_id' => $manuscript_image_mime_id,
            'mime' => image_type_to_mime_type($manuscript_image_mime_id),
            'size' => getimagesize($imagePath)
        ];
    }

    /**
     *  save copyrighted image from original adding copyright text
     *  update its name in db
     * @return void
     */
    public function updateFromOriginal()
    {
        $this->resizeOriginalImage();

        $details = $this->details(true);
        if (!is_array($details)) {
            return;
        }
        $original_image = $this->getFullPath(true);
        $width = $details['size'][0];
        $height = $details['size'][1];
        $extension = $details['info']['extension'];
        if ($extension !== 'jpg' && $extension !== 'jpeg') {
            return 'Error Extension: ' . $extension;
        }
        $img = imagecreatefromjpeg($original_image);

        $text = $this->getCopyrightText();

        if ($text) {
            $black = imagecolorallocate($img, 0, 0, 0);
            $white = imagecolorallocatealpha($img, 255, 255, 255, 50);
            $font = $this->f3->get('MR_PATH') . 'public/resources/frontend/fonts/MR/Gentium_Basic/GentiumBasic-Regular.ttf';

            $fontSize = $this->getCopyrightFontSize();
            //$logger->write($this->name . ' w: ' . $width . ' fontSize: ' . $fontSize);

            imagefilledrectangle($img, 0, $height - 50, $width, $height, $white);
            $imagettftext = imagettftext(
                $img,
                $fontSize,
                0,
                10,
                $height - $fontSize * 2,
                $black,
                $font,
                $text
            );
        }

        imagejpeg($img, $this->getFullPath(), 100);
        if (!$this->name) {
            $contentDecoded = json_decode($this->content);
            $this->name = str_replace('_original', '', $contentDecoded->name);
        }

        $this->save();

        $this->updateContent(['md5' => md5_file($this->getFullPath())]);
        return 'Success';
    }

    /**
     * return copyright text of image
     *
     * @return string
     */
    public function getCopyrightText()
    {
        $contentDecoded = json_decode($this->content);
        if (isset($contentDecoded->copyright)) {
            return $contentDecoded->copyright;
        }

        return '';
    }

    /**
     * return copyright font size
     *
     * @return string
     */
    public function getCopyrightFontSize()
    {
        $contentDecoded = json_decode($this->content);
        if (isset($contentDecoded->fontsize)) {
            return $contentDecoded->fontsize;
        }

        $details = $this->details(true);
        $width = $details['size'][0];

        $fontSize = 12;
        if ($width > 1500) {
            $fontSize = 24;
        } elseif ($width > 1000) {
            $fontSize = 18;
        }

        return $fontSize;
    }



    /**
     * resizeOriginalImage if needed: check max width + height
     *
     * @return void
     */
    public function resizeOriginalImage()
    {
        $maxWplusH = 2000 + 2000;

        $details = $this->details(true);
        if (!is_array($details)) {
            return;
        }
        $w = $details['size'][0];
        $h = $details['size'][1];

        if ($w + $h <= $maxWplusH) {
            return;
        }

        $factor = ($w + $h) / $maxWplusH;
        $newwidth = (int)($w / $factor);
        $newheight = (int)($h / $factor);

        $original_image = $this->getFullPath(true);
        $extension = $details['info']['extension'];
        if ($extension !== 'jpg' && $extension !== 'jpeg') {
            return 'Error Extension: ' . $extension;
        }
        $src = imagecreatefromjpeg($original_image);

        $dst = imagecreatetruecolor($newwidth, $newheight);
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $w, $h);
        imagejpeg($dst, $this->getFullPath(true), 100);
    }

    /**
     * check if exist original image, the one without copyright
     *
     * @return boolean
     */
    public function originalExists()
    {
        return file_exists($this->getFullPath(true));
    }

    /**
     * remove record from db
     * and its related files (ex. delete imagefolios and partners)
     *
     * @return void
     */
    public function remove(bool $deleteFiles = true)
    {
        if ($deleteFiles) {
            if (is_file($this->getFullPath(true))) {
                unlink($this->getFullPath(true));
            }
            if (is_file($this->getFullPath())) {
                unlink($this->getFullPath());
            }
            $dirname = dirname($this->getImagePath());
            if ($dirname !== $this->manuscript->getFullPath()) {
                array_map('unlink', glob("$dirname/*.*"));
                //echo  "\n *** {$dirname} ";
                if (is_dir($dirname)) {
                    rmdir($dirname);
                }
            }
        }


        $this->erase();
    }
}
