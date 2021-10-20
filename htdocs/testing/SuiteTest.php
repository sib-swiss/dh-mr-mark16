<?php
/**
 *
 * before run sync data from dev and delete testinda daabaes (it will be copied from current one)
 * ./copy-data-from-dev.sh && rm htdocs/testing/data/database-test.sqlite
 *
 * $argv
 *  1: optional, filename (without extension and path)
 *  2: optional, method to run
 *
 * Examples
 *
 * run all tests
 *  php testing/SuiteTest.php
 *
 * run specific testFile
 *  php testing/SuiteTest.php PresentationApiTest
 *
 * run specific testFile->method
 *  php testing/SuiteTest.php ManuscripContentImageTest testUpdatePartnerUrl
 */

require __DIR__ . '/../vendor/autoload.php';

$tests = glob(__DIR__ . '/*Test.php');
foreach ($tests as $testFullpath) {
    $testClass = substr(str_replace(__DIR__ . '/', '', $testFullpath), 0, -4);
    if ($testClass != 'SuiteTest'
        && (
            count($argv) == 1
            || $argv[1] == $testClass
        )
    ) {
        $namespacedClass = "Tests\\$testClass";
        (new $namespacedClass)->run(count($argv) > 2 ? $argv[2] : null);
    }
}
