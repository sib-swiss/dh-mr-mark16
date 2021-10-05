<?php

namespace Tests;

require __DIR__ . '/../vendor/autoload.php';
$f3 = require_once __DIR__ . '/../inc/bootstrap-app.php';
$f3->set('MR_CONFIG->routes->ttl->debug', true);
require __DIR__ . '/../inc/routes.php';

use Base;
use classes\Db\CreateDb;
use classes\Db\ManuscriptContentFilesystemSeeder;
use classes\Db\ManuscriptContentNakalaSeeder;
use classes\Models\Manuscript;
use Test;

/**
 * TestCase
 */
class TestCase
{
    public $f3;
    protected $doEcho;
    protected $test;

    public function __construct()
    {
        $this->doEcho = true;

        $this->f3 = Base::instance();
        $this->f3->set('QUIET', true);
        $this->f3->set('ENV', '/data/testing');
        $this->test = new Test();
        $this->checkDbSeeded();
    }

    /**
     * run all test* method
     *
     * @return void
     */
    public function run(string $methodToRun = null)
    {
        $class = new static();
        $this->echo("\n" . get_called_class());

        foreach (get_class_methods($class) as $testMethod) {
            if (
                strpos($testMethod, 'test') === 0
                &&
                (!$methodToRun
                    || $methodToRun == $testMethod)
            ) {
                $this->echo("\n\t" . $testMethod);
                $this->check($class->$testMethod());
                //$this->echo("\n";               )
            }
        }

        $this->cleanup();
        $this->echo("\n");
    }

    /**
     * seedDb test if force or not seeded
     *
     * @return void
     */
    public function checkDbSeeded(bool $force = false)
    {
        $dtestingDataFolder = __DIR__ . '/data';
        if (!is_dir($dtestingDataFolder)) {
            mkdir($dtestingDataFolder);
        }
        $dbTestingFullPath = $dtestingDataFolder . '/database-test.sqlite';
        $createDb = $force || !file_exists($dbTestingFullPath);
        if ($createDb) {
            @unlink($dbTestingFullPath);
            copy($this->f3->MR_DATA_DIR . '/database.sqlite',  $dbTestingFullPath);
        }
        $this->f3->set('DB', new \DB\SQL('sqlite:' . $dbTestingFullPath));
    }

    public function echo(string $message)
    {
        if ($this->doEcho) {
            echo $message;
        }
    }

    protected function cleanup()
    {
        // to be impletend in extendend class
    }

    public function check(object $expect)
    {
        if ($expect->passed()) {
            echo ' ==> Passes';
        } else {
            foreach ($expect->results() as $result) {
                //echo $result['text'].'<br>';
                if ($result['status']) {
                    //echo 'Pass';
                } else {
                    echo "\nFail: " . $result['text'] . "   " . $result['source'];
                }
            }
        }
    }
}
