<?php

namespace Tests;

require __DIR__ . '/../vendor/autoload.php';

use classes\Db\ManuscriptContentNakalaSeeder;
use classes\Models\Manuscript;
use Test;

class ManuscriptTest extends TestCase
{
    public function testManuscriptGetTeIUrl()
    {
        foreach (Manuscript::all() as $manuscript) {
            foreach ($manuscript->contentsMeta() as $contentMeta) {
                //echo "\n" . $contentMeta->getTeiUrl();
                $this->test->expect(
                    $contentMeta->getTeiUrl() !== '',
                    $contentMeta->getTeiUrl() . ' Should be NOT empty'
                );
            }
        }

        return $this->test;
    }

    public function testManuscriptGetNakalaUrl()
    {
        $manuscripts = [
            'GA2604' => 'https://api.nakala.fr/datas/10.34847/nkl.cfac1n0c'
        ];
        $this->test = new Test();
        foreach ($manuscripts as $name => $url) {
            $manuscript = Manuscript::findBy('name', $name);
            if (!$manuscript) {
                (new ManuscriptContentNakalaSeeder($url))->handle();
                $manuscript = Manuscript::findBy('name', $name);
            }

            $this->test->expect(
                $manuscript->name == $name,
                $manuscript->name . ' Should be ' . $name
            );
            $getNakalaUrl = $manuscript->getNakalaUrl();
            $this->test->expect(
                $getNakalaUrl == $url,
                $getNakalaUrl . ' Should be ' . $url
            );

            $this->test->expect(
                count($manuscript->contentsHtml()) > 0,
                $manuscript->name . ' count(contentsHtml()) Should > 0 '
            );

            return $this->test;
        }
    }

    public function XXtestPublishedAfterSeeding()
    {
        $this->checkDbSeeded(true);

        $manuscript = Manuscript::findBy('name', 'ARB2');

        $this->test = new Test();
        $this->test->expect(
            1 == $manuscript->published,
            $manuscript->name . ' Should be published'
        );

        $manuscript = Manuscript::findBy('name', 'GA1210');
        $this->test->expect(
            1 == $manuscript->published,
            $manuscript->name . ' Should be published'
        );

        return $this->test;
    }

    /**
     * test Show route will show expected manuscript
     *
     * @return void
     */
    public function testShowPublished()
    {
        $this->checkDbSeeded();
        $name = 'GA1230';
        $manuscript = Manuscript::findBy('name', $name);
        $manuscript->published = 1;
        $manuscript->save();
        if (!$manuscript) {
            die('Fail: Db not seeded?');
        }
        $this->f3->mock('GET /show?id=' . $manuscript->getEncodedId());
        $this->test = new Test();
        $this->test->expect(
            strpos($this->f3->get('response'), $manuscript->getDisplayname()) !== false,
            $manuscript->getDisplayname() . ' Should BE displayed'
        );
        return $this->test;
    }

    /**
     * test Show route will show expected manuscript
     *
     * @return void
     */
    public function testShowNotPublished()
    {
        $this->checkDbSeeded();
        $this->test = new Test();
        $name = 'GA1210';
        $manuscript = Manuscript::findBy('name', $name);
        if (!$manuscript) {
            die('Fail: Db not seeded?');
        }
        $manuscript->published = 0;
        $manuscript->save();
        $this->f3->mock('GET /show?id=' . $manuscript->getEncodedId());
        $this->test->expect(
            strpos($this->f3->get('response'), $manuscript->getDisplayname()) === false,
            $manuscript->getDisplayname() . ' Should not be displayed because just set to unpublished'
        );
        return $this->test;
    }

    public function testManuscriptSortedBYdctermTemporal()
    {
        //testFieldInDb;
        $previous = 0;
        $this->test = new Test();
        foreach (Manuscript::allPublished() as $manuscript) {
            $this->test->expect(
                $manuscript->temporal >= $previous ? true : false,
                $manuscript->temporal . ' shuold be greated or equals than ' . $previous,
            );
            $previous = $manuscript->temporal;
        }
        return $this->test;
    }
}
