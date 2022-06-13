<?php

namespace iiif21;

use Base;
use classes\Models\Manuscript;
use Image;
use Log;

/**
 * https://iiif.io/api/image/2.1/
 */
class ImageApi
{
    private $params;
    private $apiUrl;
    private $manuscriptPage;
    private $manuscript;
    private $manuscriptContent;
    private $img;
    private $imageFullPath;
    private $imageCacheFullPath;

    public function __construct($params, array $validExtensions = ['jpg', 'jpeg'])
    {
        $this->f3 = Base::instance();
        $this->logger = new Log(str_replace('\\', '-', __CLASS__) . '.log');
        $this->params = $params;

        $this->apiUrl = $this->f3->get('SCHEME') . '://' . $this->f3->get('SERVER.HTTP_HOST') . $this->f3->get('MR_PATH_WEB')
            . 'api/iiif/2-1/';

        $parsedId = explode('-', $this->params['id']);
        $manuscriptName = $parsedId[0];
        $this->manuscriptPage = (int)str_replace('page', '', $parsedId[1]);

        $this->manuscript = Manuscript::findBy('name', $manuscriptName);

        $manuscriptFolio = $this->manuscript->contentsFolios()[$this->manuscriptPage - 1];
        $this->manuscriptContent = $manuscriptFolio->getFolioImage();
        $this->imageFullPath = $this->manuscriptContent->getImagePath();

        $this->logger->write('imageFullPath: ' . $this->imageFullPath);
        $details = $this->manuscriptContent->details();
        if ($details['mime_id'] == IMAGETYPE_JPEG) {
            $this->img = new Image($this->imageFullPath, false, '');
        } else {
            die($details['mime_id'] . 'NOT SUPPORTED');
        }
    }

    /**
     * Return collection based on sample:
     * https://iiif.io/api/presentation/2.1/#collection

     *
     * @return void
     */
    public function info()
    {
        header('Content-Type: text/json');
        echo json_encode($this->getInfo(), JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }

    // TODO: Fix cached image issue better (please)
    // TODO2: Implement the same logic for the requested JSON manifests from Mirador
    // IDEA:  Check the cached JSON version, if exist:
    //          - Return the cached JSON version
    //          - Or, return the cached URL version from F3
    public function imageRender()
    {
        $cachepath = $this->f3->get('MR_CONFIG')->cache->path . 'images';
        if (!is_dir($cachepath)) {
            mkdir($cachepath);
        }
        $path = explode(
            '/',
            str_replace(
                $this->f3->get('MR_DATA_PATH') . '/',
                '',
                $this->imageFullPath
            )
        );
        if (count($path) > 1) {
            $cachepath .= '/' . str_replace(' ', '_', $path[0]);
        }

        $this->imageCacheFullPath = $cachepath
            . '_' . $this->manuscriptPage
            . '_' . str_replace([':', ','], '-', $this->params['region'])
            . '_' . str_replace(['!', ','], '-', $this->params['size'])
            . '_' . str_replace(['!', ','], '-', $this->params['rotation'])
            . '_' . str_replace(['!', ','], '-', $this->params['quality'])
            . '_' . str_replace(['!', ','], '-', $this->params['format']);
        // die($this->imageCacheFullPath);
        // $this->logger->write('imageFullPath: ' . $this->imageFullPath);
        // $this->logger->write('imageCacheFullPath: ' . $this->imageCacheFullPath);

        // Disabled problematic code
        /* if (
            $this->f3->get('MR_CONFIG')->cache->clear !== true
            && is_file($this->imageCacheFullPath)
        ) {
            // Return image from the filesystem
            // and set caching to browser 86400 = 24hours in seconds (previously)
            // now, reading dedicated TTL value from JSON config
            header('Content-Type: ' . $this->manuscriptContent->details()['mime']);
            header('Pragma: public');
            header('Cache-Control: max-age=' . $this->f3->get('MR_CONFIG')->routes->ttl->images);
            header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
            readfile($this->imageCacheFullPath);

            exit;
        } */
        // Temporary testing / fixing code
        if (is_file($this->imageCacheFullPath) &&
            $this->f3->get('MR_CONFIG')->cache->clear !== true &&
            $this->f3->get('MR_CONFIG')->routes->ttl->debug !== true) {
            $this->logger->write('CachedImageFound: ' . $this->imageCacheFullPath);
            header('Content-Type: ' . $this->manuscriptContent->details()['mime']);
            if ($this->f3->get('MR_CONFIG')->routes->ttl->debug === true) {
                header('Cache-Control: no-cache, no-store, must-revalidate');
                header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + $this->f3->get('MR_CONFIG')->routes->ttl->off));
            }
            else {
                header('Cache-Control: max-age=' . $this->f3->get('MR_CONFIG')->routes->ttl->images);
                header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + $this->f3->get('MR_CONFIG')->routes->ttl->images));
            }
            readfile($this->imageCacheFullPath);

            exit;
        }

        //$manuscriptImage = new ManuscriptImage($this->manuscriptName, $this->manuscriptPage, ['jpg', 'jpeg']);
        $this->region($this->params['region'])
            ->size($this->params['size'])
            ->rotation($this->params['rotation'])
            ->quality($this->params['quality'])
            ->format($this->params['format'])
            ->render();
    }

    public function render()
    {
        if ($this->manuscriptContent->details()['mime_id'] == IMAGETYPE_JPEG) {
            //$this->logger->write('SAVIG IMAGE INTO: ' . $this->imageCacheFullPath);
            $this->f3->write($this->imageCacheFullPath, $this->img->dump('jpeg', 100));
            $this->img->render('jpeg', 100);
        }
    }

    public function getInfo()
    {
        return [
            '@context' => 'http://iiif.io/api/image/2/context.json',
            '@id' => $this->apiUrl . 'images/' . $this->manuscript->name . '-page' . $this->manuscriptPage,
            'protocol' => 'http://iiif.io/api/image',
            'width' => $this->manuscriptContent->details()['size'][0],
            'height' => $this->manuscriptContent->details()['size'][1],
            /*
            $api_response->sizes     = [];
            'tiles'     => [];
            */
            'profile' => ['http://iiif.io/api/image/2/level2.json']
        ];
    }

    private function region($region)
    {
        $this->logger->write('region: ' . $region);
        if ($region == 'full') {
            /**
             * Nothing to do:
             * The complete image is returned, without any cropping.
             */
            return $this;
        }

        if ($region == 'square') {
            /**
             * The region is defined as an area where the width and height are both equal to the length
             * of the shorter dimension of the complete image.
             *
             * The region may be positioned anywhere in the longer dimension of the image content at the server’s discretion,
             * and centered is often a reasonable default.
             */
            return $this;
        }

        /**
         * The region of the full image to be returned is specified in terms of absolute pixel values.
         *
         * The value of x represents the number of pixels from the 0 position on the horizontal axis.
         * The value of y represents the number of pixels from the 0 position on the vertical axis.
         *
         * Thus the x,y position 0,0 is the upper left-most pixel of the image.
         * w represents the width of the region and h represents the height of the region in pixels.
         *
         *
         */

        // Store received coordinates

        // handling pct?
        if (substr($region, 0, 4) == 'pct:') {
            /*
            pct:x,y,w,h
            The region to be returned is specified as a sequence of percentages of the full image’s dimensions,
             as reported in the image information document.
             Thus, x represents the number of pixels from the 0 position on the horizontal axis,
             calculated as a percentage of the reported width. w represents the width of the region,
             also calculated as a percentage of the reported width. The same applies to y and h respectively.
             These may be floating point numbers.
            */
            die('ToDo Pct: https://iiif.io/api/image/2.1/#region');
        } else {
            list($x, $y, $w, $h) = explode(',', $region);
        }

        // Cast values as INT
        $org_x = (int)$x;
        $org_y = (int)$y;
        $org_w = (int)$w;
        $org_h = (int)$h;

        // Check received coordinates
        if ($org_w !== 0 && $org_h === 0) {
            // Return status code 400 on error
            $this->f3->error(
                400,
                "The requested region's height or width is zero, or if the region is entirely outside the bounds of the reported dimensions"
            );
        } elseif ($org_w === 0 && $org_h !== 0) {
            // Return status code 400 on error
            $this->f3->error(
                400,
                "The requested region's height or width is zero, or if the region is entirely outside the bounds of the reported dimensions"
            );
        } else {
            // Crop image according to coordinates
            $org_x2 = $org_w + $org_x;
            $org_y2 = $org_h + $org_y;

            $this->logger->write('region.crop: org_x:' . $org_x
                . ' org_x:' . $org_y
                . ' org_w:' . $org_w
                . ' org_h:' . $org_h
                . ' org_x2:' . $org_x2
                . ' org_y2:' . $org_y2);
            $this->img->crop($org_x, $org_y, $org_x2, $org_y2);
        }
        return $this;
    }

    private function size($size)
    {
        $this->logger->write('size: ' . $size);
        if ($size == 'full') {
            /**
             * Nothing to do:
             * The image or region is not scaled, and is returned at its full size.
             */
            return $this;
        }

        if ($size == 'max') {
            /**
             * The image or region is returned at the maximum size available, as indicated by maxWidth, maxHeight, maxArea in the profile description.
             * This is the same as full if none of these properties are provided.
             */

            return $this;
        }

        /**
         * Possible values:
         *
         * w,   = The image or region should be scaled so that its width is exactly equal to w, and the height will be a calculated value that maintains the aspect ratio of the extracted region.
         * ,h   = The image or region should be scaled so that its height is exactly equal to h, and the width will be a calculated value that maintains the aspect ratio of the extracted region.
         * w,h  = The width and height of the returned image are exactly w and h. The aspect ratio of the returned image may be different than the extracted region, resulting in a distorted image.
         * !w,h = The image content is scaled for the best fit such that the resulting width and height are less than or equal to the requested width and height.
         *        The exact scaling may be determined by the service provider, based on characteristics including image quality and system performance.
         *        The dimensions of the returned image content are calculated to maintain the aspect ratio of the extracted region.
         */

        // Parse given size
        $parsed_size = explode(',', $size);

        // Rendering possible values
        if ($parsed_size[0] !== '' && $parsed_size[1] === '') {
            $this->img->resize($parsed_size[0], null, true);
        } elseif ($parsed_size[0] === '' && $parsed_size[1] !== '') {
            $this->img->resize(null, $parsed_size[1], true);
        } elseif ($parsed_size[0] !== '' && $parsed_size[1] !== '') {
            $this->img->resize($parsed_size[0], $parsed_size[1], false, false);
        } else {
            $this->img->resize($parsed_size[0], $parsed_size[1], false);
        }
        return $this;
    }

    private function rotation($rotation)
    {
        $this->logger->write('rotation: ' . $rotation);
        $this->img->rotate(-$rotation);
        return $this;
    }

    private function quality($quality)
    {
        $this->logger->write('quality: ' . $quality);
        if ($quality == 'color') {
            return $this;
        }
        if ($quality == 'gray') {
            $this->img->grayscale();
            //imagefilter($this->img, IMG_FILTER_GRAYSCALE);
            return $this;
        }
        if ($quality == 'bitonal') {
            // ToDo
            return $this;
        }
        if ($quality == 'default') {
            return $this;
        }
        die('QUALITY NOT SUPPORTED ' . $quality);
        /*
        4.4. Quality
        The quality parameter determines whether the image is delivered in color, grayscale or black and white.
        Quality Parameter Returned
        color   The image is returned in full color.
        gray    The image is returned in grayscale, where each pixel is black, white or any shade of gray in between.
        bitonal The image returned is bitonal, where each pixel is either black or white.
        default The image is returned using the server’s default quality (e.g. color, gray or bitonal) for the image.
        */
    }

    private function format($format)
    {
        // supported only jpg?
        /*
        jpg image/jpeg
        tif image/tiff
        png image/png
        gif image/gif
        jp2 image/jp2
        pdf application/pdf
        webp    image/webp
        */
        $this->logger->write('format: ' . $format);
        return $this;
    }
}
