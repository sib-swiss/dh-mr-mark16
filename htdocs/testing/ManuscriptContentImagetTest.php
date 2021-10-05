<?php

namespace Tests;

require __DIR__ . '/../vendor/autoload.php';

use classes\Models\Manuscript;
use Test;
use classes\Models\ManuscriptContentImage;
use classes\Models\ManuscriptContentMeta;

class ManuscriptContentImagetTest extends TestCase
{
    private $manuscripContent;
    private $manuscript;

    public function setup()
    {
        $this->manuscript = Manuscript::findBy('id', 9999);
        if (!$this->manuscript) {
            $this->manuscript = Manuscript::store([
                'id' => 9999,
                'name' => 'TEST'
            ]);
        }
        //$mImage = ManuscriptContentImage::findBy('id', 113);
        $this->manuscripContent = new ManuscriptContentImage();

        $this->manuscripContent->name = 'test' . time() . '.jpg';
        $this->manuscripContent->manuscript_id = $this->manuscript->id;
        $this->manuscripContent->extension = 'jpg';
        $this->manuscripContent->content = json_encode(['test' => 1]);
        $this->manuscripContent->save();
        $this->test = new Test();
    }

    /**
     * testUpdateCopyright
     *
     * @return void
     */
    public function testUpdateCopyright()
    {
        $this->setup();
        $newCopyright = 'xxx' . time();
        $this->manuscripContent->updateContent(['copyright' => $newCopyright]);

        $this->manuscripContent = ManuscriptContentImage::findBy('id', $this->manuscripContent->id);
        $this->test->expect(
            $newCopyright == $this->manuscripContent->getCopyrightText(),
            $this->manuscripContent->getCopyrightText() . "\nExcpected to be quals to\n" . $newCopyright
        );
        return $this->test;
    }

    /**
     * testUpdateImageOriginal
     *
     * @return void
     */
    public function testUpdateImageOriginal()
    {
        $this->setup();
        //$newBase64EncodeContent = base64_encode(file_get_contents('https://via.placeholder.com/300.jpg/09f/fff'));
        $newBase64EncodeContent = '/9j/4AAQSkZJRgABAQEAYABgAAD//gA+Q1JFQVRPUjogZ2QtanBlZyB2MS4wICh1c2luZyBJSkcgSlBFRyB2NjIpLCBkZWZhdWx0IHF1YWxpdHkK/9sAQwAIBgYHBgUIBwcHCQkICgwUDQwLCwwZEhMPFB0aHx4dGhwcICQuJyAiLCMcHCg3KSwwMTQ0NB8nOT04MjwuMzQy/9sAQwEJCQkMCwwYDQ0YMiEcITIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIyMjIy/8AAEQgBLAEsAwEiAAIRAQMRAf/EAB8AAAEFAQEBAQEBAAAAAAAAAAABAgMEBQYHCAkKC//EALUQAAIBAwMCBAMFBQQEAAABfQECAwAEEQUSITFBBhNRYQcicRQygZGhCCNCscEVUtHwJDNicoIJChYXGBkaJSYnKCkqNDU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6g4SFhoeIiYqSk5SVlpeYmZqio6Slpqeoqaqys7S1tre4ubrCw8TFxsfIycrS09TV1tfY2drh4uPk5ebn6Onq8fLz9PX29/j5+v/EAB8BAAMBAQEBAQEBAQEAAAAAAAABAgMEBQYHCAkKC//EALURAAIBAgQEAwQHBQQEAAECdwABAgMRBAUhMQYSQVEHYXETIjKBCBRCkaGxwQkjM1LwFWJy0QoWJDThJfEXGBkaJicoKSo1Njc4OTpDREVGR0hJSlNUVVZXWFlaY2RlZmdoaWpzdHV2d3h5eoKDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uLj5OXm5+jp6vLz9PX29/j5+v/aAAwDAQACEQMRAD8ArUUUV+iH5mFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFdZH4Ljihh/tLWrSxuZlDJbyckZ9eRisqtenStzvf+uhvRw9StfkW3y/M5Oir+p6Rd6VqjafOmZwQF2ch89CPrXQDwKf+PY6xZjVNu77FnnpnGc9fw/SpniqMEpSlo9v67FQwlacnGMdVv8A5evkchRVm3sLm51BLCOI/aXk8sIeMNnHPpiujuPBO2CdbPV7S8vbdS0trH94AdcHPJ/AU6mJpU2lN7ipYarVTcI3t/Xz+RydFFFbHOFFFFABRW3ofhyTV4J7uW6hs7KAhXnmPGfQfn+op+seGTp1guoWl9Bf2JfyzLFwVb0I5x+dYPE0lU9nfX+uux0LC1nT9ry6f1rbexg0VvaP4a/tGxbULzUINPsg/lrLLyXb0AyM1V1zQ59DuY43ljnhmXfDPGcrItNYik6ns09RSw1WNP2rWn9fMy6K6TTfCa3Onw3uoarbadHcE+QsvLP74yMD3rJ1fSrnRdRezudu9QCGXlWU9CKIYilObhF6oJ4arCCqSWj/AK+RRooorYwCiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigB0bmKVJAASrBsHocV0tnpmpeO9VvL7zbaF12eZncAARgbRz/AHfWuetY45byCOZykTyKrsP4QTya0Na0ufQdektIml3IwMMg4ZgRwRj+lc9azlywdp20dr6XVzqo3UeaavC6ur21s7fqdTKFuPiXplnsfFnGkW6RcFyis27/AOvXIyajL/wkTakH/efajMD/AMCz+VdhfXhg8e6BJdELcLaxJcE9nYMDn/vquYfRpz4ubShG283W3p/Du+99Mc1xYVxSvLbkX5u/6Hdi1Ju0N+d/krfqdUkS2XxG1m5RFH2e1kuV+pRST+bGuU8LXUlv4r0+UMdzzhGJPUN8p/nXTw3UV78SdUhEgC3UMlqGJ4yEA/mprnvCunTyeLrWF42Q20vmS5H3NnPPpyMfjU0rKlP2n8kfyf6l1butD2e3tJffdfoZ2swLba5fwIoVY7iRVA7AMcVpeFLXS9SvZdO1CL97cIRbT7mHlyY44Bwfx7j3rK1W4W71i9uEOVlnd1PsWJFWPD2mz6rrlrbQMyNvDtIvWNRyW/w98V3VE/q/vStpv2PPpNfWVyx5k3t3NeDQYNI0bUb/AFu23yK5t7WEuy7pO7cEZA/ofauVr0Dxu0eu6TFqthcGWCzlaCZOwOcB/wAePzFef1ngakqkHUm9W9V2t0/X5mmYU4Uqip017qWj736/p8jVtr27v7C18PRCFY5LoMrEEEu3yjJ9OfSui1TR5vC3g25tLlhPLfTpzECY4wvPUjqawZNEX/hFYNYhd5CZminTHEfof5fnWlp81x/wrzV1uWZrcyxLbbzwG3Att/AD9ayrWbi6b93mV13d/wCmbULpSjUXvODs+iVu3nt5Mi8TOYtF8PWY4RbPz8DuXOc/p+tFyxufh1ZyPgta37Qqe4Vl3Efnil8Qxm58OaBqCfNGtubZyP4WU8A/Xn8qXUIzY+ANOt5crLdXbXIQ9doXaD/I/jShbkprrzP83cc7+0qPpyL8o2G+OXK67FZ9EtLaKJVHQfLn+tHiJjc+G/Dl4+DI0EkLN3IRgB/Wl8axtLqNpqKfNFe2sbqw6bgMEfXp+dHihDZaNoGmvkTQ27yyIeqlyDg+/Boo25aCW+v5O/4hXvz4hvbT/wBKVvwOYooor0zyQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAK6a08d6xa2qQEW0xjXaks0eXX8ciuZorOrRp1Vaorm1KvUou9OVie7u5767kurmVpJ5G3M56k1v/8ACea2LPyN8PmbNn2ny/3uPrnH6VzNFKeHpVElOKdth08RVptuEmr7kkU8sM6TxyMsqMHVweQRzmugvfHGsX1i9q7QR+Yu2SWKPa7j0Jz/ACArm6KJ0KdRpzim0KnXq004wk0nuFaema5c6RbXcVrHCHuk8tpmUl1X0U54/KsyirnCM1yyV0RCcoS5ouzNPSdcutIiuoYo4ZoLqPy5YpgSpHrwQc8n86zKKKFCMZOSWr3CVSUoqLei2NbRvEWoaEZBaMjRSffilXcpPrj1pda8SahrojS6MaQxnKQwrtQH1xWRRUfV6XtPacq5u5p9Yq+z9lzPl7GzovifUNDikht/KkgkO5oZl3Ln16iqmravea1em6vZN742qAMKo9AKo0U1QpqbqKK5u4nXqumqbk+VdDf0nxhqmj2YtIfImgU7kWdN2w+2CO/NZF9fXOpXkl3dymSaQ5ZjVeilGhThNzjFJsJ16s4KEpNpbIKKKK1MQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK6/UrJZ9TvbCUabF5l2IbMQCIOmZQPmEfOAuc7uc4rKrWVNpPr/wP8zelRdRNrp/wf8AI5CitaS1014BdQ/akgiuUhmEjKWZWBIZcDg4VuDnHHNWT4dEDI1xKdqyy+eqfeWNd2GHufLk/IetJ4iC30GsNN/DqYFFb9jodtPYW0txOkbXIYq7XUUaxAMVBKsdzcg9McetUktrGPQ0u5hcPcSzSxIqOFUbVQgnIJPL9O/qKFXi3Za62/P/ACE8PNK70Vr/AJf5ozacsbursqMyoNzkDO0ZAyfQZIH4it3VNDtrC3uAJ08+2IBJuom805AIEancuM55zwDnFSNa2Vla67axfaDcwQLHI7kFHInjDEADI5HHJyPSp+sxaTjrd/5f5l/VZxbU9LL9H/kc8sbursqMyoNzkDO0ZAyfQZIH4im1raJ5H2fVvtIkMQswWEZAY/vosAE9Occ05dMtZr22iieVUvYC9uHIysmWUKxxyCykZ46g1TrJNp9P8rkKg5RTj1/zsY9FXL20Wzit433i6ZfMlQ9EB+6MeuOT/vD3q9NYWsmnaXI19aWjvbMWWRJNznzpBuOxCDwAOTnj6VTqxST7iVGTbXb/AIH+Zi0V1CutrrD2kUVlLANP84MbVG3OLQMGBZd2CwDY475HJqDTPI1Cxv8A7XFbiW5nggSVYVTymKyEEAABQWVc47ZrL6w1Hma0sn95r9WTlyp63a+456iugv7SGy8OvatAgvILiEzSFRvVnSUlM9cAKuR65rO1yNItf1KONFSNLqVVVRgKAxwAPStKdZTdl/W3+ZnUoOmrt9v1/KxQorUt2Wy0dbyOCGSeSdot0sYkEYVVP3WyMnceo/hosWjuLi8vJLaFmgg81YQuEZtyr930GS2OnHpQ6m7toJUtlfV/kZdFdJpvlapcafcXFrbq66jBAwjhVElRiSQVA28bfT+LmrWl2VmdWtNUkt4ntLyWKOKBkBUSO211x0wuGI+qVlPFKF01qvz7G0MI52cXo/y7nI0UUV1HGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAVLNczT3cl1JITPJIZGccHcTknjpzUVFFluO72Ld5qd3fKq3EgZQxbCoqAsepO0DJ9zzTpNWv5TMXuGJmiWGTgfMi4wOnsOfr6mqVFR7OCVrIt1Zt3uy3b6nd2sPkxOmwEld8auUJ7qSCVPuMVA08jWyW5b90js6rgcMwUE/+Or+VR0U1CKd0hOcmrNlubU7ue28iR0ZcAFvLUOwHQF8biOBwT2FSSa1fy2kls84MUiqkn7tdzhSCMtjJxgdTVCil7KHZD9rU/mf3lmyv7nT3ke2kCGRPLfKBgy5BwQQeMgVPBfRzakl3qTyuIwCqRIoDbcYXqAq+4B+lZ9FDpxd3bV9eoRqSVlfRdOhNdXMt5dy3M7bpZXLsfc02SeSaOFJGysKbIxgcLuLY/NifxqOiqUUrLsS5Ntu+5Y+33Pn+f5n7zyfI3bR9zZ5eP++eM9fxqNZ5Ftntw37p3V2XA5ZQwB/8eb86joo5Y9g5pdyRZ5Ftntw37p3V2XA5ZQwB/wDHm/Ord3rF3fLILgWzNKdzutpErk5zncFBzn3qhRScIt3a1Gqk0rJ6F1NXvUuLifzVZ7hy8wkjV0diSclSCvc9uM1GNQulvftiylZ/7ygDjGMY6Yxxjpiq1FHs4dkP2k+7L0msX0lzbzmVVe3cPEI4lRUYHOQoAGcgducVVt55LW5iuIW2yxOHRsA4YHIPNR0UKEUrJCdSbd29QoooqiD/2Q==';
        $this->manuscripContent->updateImage($newBase64EncodeContent, true);
        $this->manuscripContent = ManuscriptContentImage::findBy('id', $this->manuscripContent->id);
        $this->test->expect(
            $newBase64EncodeContent == $this->manuscripContent->imageContent(true),
            "\nExcpected base64Encode Original are the same"
        );
        return $this->test;
    }

    /**
     * testUpdateImage
     *
     * @return void
     */
    public function testUpdateImage()
    {
        $this->setup();
        //$newBase64EncodeContent = base64_encode(file_get_contents('https://via.placeholder.com/300.jpg/09f/fff'));

        $newBase64EncodeContent = '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56k1v/8ACea2LPyN8PmbNn2ny/3uPrnH6VzNFKeHpVElOKdth08RVptuEmr7kkU8sM6TxyMsqMHVweQRzmugvfHGsX1i9q7QR+Yu2SWKPa7j0Jz/ACArm6KJ0KdRpzim0KnXq004wk0nuFaema5c6RbXcVrHCHuk8tpmUl1X0U54/KsyirnCM1yyV0RCcoS5ouzNPSdcutIiuoYo4ZoLqPy5YpgSpHrwQc8n86zKKKFCMZOSWr3CVSUoqLei2NbRvEWoaEZBaMjRSffilXcpPrj1pda8SahrojS6MaQxnKQwrtQH1xWRRUfV6XtPacq5u5p9Yq+z9lzPl7GzovifUNDikht/KkgkO5oZl3Ln16iqmravea1em6vZN742qAMKo9AKo0U1QpqbqKK5u4nXqumqbk+VdDf0nxhqmj2YtIfImgU7kWdN2w+2CO/NZF9fXOpXkl3dymSaQ5ZjVeilGhThNzjFJsJ16s4KEpNpbIKKKK1MQooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKK6/UrJZ9TvbCUabF5l2IbMQCIOmZQPmEfOAuc7uc4rKrWVNpPr/wP8zelRdRNrp/wf8AI5CitaS1014BdQ/akgiuUhmEjKWZWBIZcDg4VuDnHHNWT4dEDI1xKdqyy+eqfeWNd2GHufLk/IetJ4iC30GsNN/DqYFFb9jodtPYW0txOkbXIYq7XUUaxAMVBKsdzcg9McetUktrGPQ0u5hcPcSzSxIqOFUbVQgnIJPL9O/qKFXi3Za62/P/ACE8PNK70Vr/AJf5ozacsbursqMyoNzkDO0ZAyfQZIH4it3VNDtrC3uAJ08+2IBJuom805AIEancuM55zwDnFSNa2Vla67axfaDcwQLHI7kFHInjDEADI5HHJyPSp+sxaTjrd/5f5l/VZxbU9LL9H/kc8sbursqMyoNzkDO0ZAyfQZIH4im1raJ5H2fVvtIkMQswWEZAY/vosAE9Occ05dMtZr22iieVUvYC9uHIysmWUKxxyCykZ46g1TrJNp9P8rkKg5RTj1/zsY9FXL20Wzit433i6ZfMlQ9EB+6MeuOT/vD3q9NYWsmnaXI19aWjvbMWWRJNznzpBuOxCDwAOTnj6VTqxST7iVGTbXb/AIH+Zi0V1CutrrD2kUVlLANP84MbVG3OLQMGBZd2CwDY475HJqDTPI1Cxv8A7XFbiW5nggSVYVTymKyEEAABQWVc47ZrL6w1Hma0sn95r9WTlyp63a+456iugv7SGy8OvatAgvILiEzSFRvVnSUlM9cAKuR65rO1yNItf1KONFSNLqVVVRgKAxwAPStKdZTdl/W3+ZnUoOmrt9v1/KxQorUt2Wy0dbyOCGSeSdot0sYkEYVVP3WyMnceo/hosWjuLi8vJLaFmgg81YQuEZtyr930GS2OnHpQ6m7toJUtlfV/kZdFdJpvlapcafcXFrbq66jBAwjhVElRiSQVA28bfT+LmrWl2VmdWtNUkt4ntLyWKOKBkBUSO211x0wuGI+qVlPFKF01qvz7G0MI52cXo/y7nI0UUV1HGFFFFABRRRQAUUUUAFFFFABRRRQAUUUUAFFFFABRRRQAVLNczT3cl1JITPJIZGccHcTknjpzUVFFluO72Ld5qd3fKq3EgZQxbCoqAsepO0DJ9zzTpNWv5TMXuGJmiWGTgfMi4wOnsOfr6mqVFR7OCVrIt1Zt3uy3b6nd2sPkxOmwEld8auUJ7qSCVPuMVA08jWyW5b90js6rgcMwUE/+Or+VR0U1CKd0hOcmrNlubU7ue28iR0ZcAFvLUOwHQF8biOBwT2FSSa1fy2kls84MUiqkn7tdzhSCMtjJxgdTVCil7KHZD9rU/mf3lmyv7nT3ke2kCGRPLfKBgy5BwQQeMgVPBfRzakl3qTyuIwCqRIoDbcYXqAq+4B+lZ9FDpxd3bV9eoRqSVlfRdOhNdXMt5dy3M7bpZXLsfc02SeSaOFJGysKbIxgcLuLY/NifxqOiqUUrLsS5Ntu+5Y+33Pn+f5n7zyfI3bR9zZ5eP++eM9fxqNZ5Ftntw37p3V2XA5ZQwB/8eb86joo5Y9g5pdyRZ5Ftntw37p3V2XA5ZQwB/wDHm/Ord3rF3fLILgWzNKdzutpErk5zncFBzn3qhRScIt3a1Gqk0rJ6F1NXvUuLifzVZ7hy8wkjV0diSclSCvc9uM1GNQulvftiylZ/7ygDjGMY6Yxxjpiq1FHs4dkP2k+7L0msX0lzbzmVVe3cPEI4lRUYHOQoAGcgducVVt55LW5iuIW2yxOHRsA4YHIPNR0UKEUrJCdSbd29QoooqiD/2Q==';
        $this->manuscripContent->updateImage($newBase64EncodeContent);
        $this->manuscripContent = ManuscriptContentImage::findBy('id', $this->manuscripContent->id);
        $this->test->expect(
            $newBase64EncodeContent == $this->manuscripContent->imageContent(),
            "\nExcpected base64Encode Original are the same"
        );
        return $this->test;
    }

    /**
     * testUpdatePartnerUrl
     *
     * @return void
     */
    public function testUpdatePartnerUrl()
    {
        $this->setup();
        $this->manuscripContent->url = 'https://www.defaulturl.com';
        $newUrl = 'https://www.google.com?' . time();

        $this->manuscripContent->url = $newUrl;
        $this->manuscripContent->save();

        $this->manuscripContent = ManuscriptContentImage::findBy('id', $this->manuscripContent->id);
        $this->test->expect(
            $newUrl == $this->manuscripContent->url,
            "\nExcpected url:" . $this->manuscripContent->url . ' Equals to:' . $newUrl
        );
        return $this->test;
    }

    /**
     * testUpdatePartnerUrl
     *
     * @return void
     */
    public function testCreateNewPartnerImageandUrl()
    {
        $this->setup();
        $url = 'http://www.google.com?' . microtime();
        $file = __DIR__ . '/data/partner-SCMS.jpg';
        $newBase64EncodeContent = base64_encode(file_get_contents($file));

        $this->manuscript->createPartner($newBase64EncodeContent, $url);

        $contentPartners = $this->manuscript->contentPartners();

        $partner = end($contentPartners);

        $this->test->expect(
            $partner->url == $url,
            "\nExcpected url:" . $partner->url . ' Equals to:' . $url
        );

        $this->test->expect(
            $partner->imageContent() == $newBase64EncodeContent,
            "\nExcpected newBase64EncodeContent: " . substr($partner->imageContent(), 0, 100)
        );
        return $this->test;
    }

    public function TODOtestFilenameStructure()
    {
        $manuscripContentMeta = ManuscriptContentMeta::findBy('id', 20);
        $manuscripContentImage = ManuscriptContentImage::findBy('id', 31);
        $manuscript = $manuscripContentImage->manuscript();
        dd([
            'manuscript' => $manuscript->getNakalaUrl(),
            'meta.name' => $manuscripContentMeta->name,
            'meta.getFolioName()' => $manuscripContentMeta->getFolioName(),
            'name' => $manuscripContentImage->name,
            'imagepth' => $manuscripContentImage->getImagePath()
        ]);
        $this->test = new Test();
    }

    /**
     * cleanup
     *
     * @return void
     */
    public function cleanup()
    {
        foreach (ManuscriptContentImage::where('manuscript_id', 9999) as $created) {
            //echo "\n Removing " . $created->id;
            @unlink($created->getImagePath(true));
            @unlink($created->getImagePath());
            $created->erase();
        }
        Manuscript::findBy('id', 9999)->erase();
    }
}
