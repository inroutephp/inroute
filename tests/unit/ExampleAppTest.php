<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\inroute;

if (!defined('INROUTE_EXAMPLE_DIR')) {
    define('INROUTE_EXAMPLE_DIR', __DIR__ . '/../../example');
}

/**
 * Test the complete exampla application
 *
 * These are not true unit tests, but rather functional tests for the complete
 * suite.
 *
 * @ runTestsInSeparateProcesses
 */
class ExampleAppTest extends \PHPUnit_Framework_TestCase
{
    public function testVoid()
    {
    }
    /*
    public static function setUpBeforeClass()
    {
        // Build the example application
        exec(INROUTE_EXAMPLE_DIR . '/build');
    }

    public function setUp()
    {
        ob_start();
    }

    public function tearDown()
    {
        ob_end_clean();
    }

    public function testDevelopmentStyle()
    {
        $uri = '/hello-world';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        include INROUTE_EXAMPLE_DIR . '/development.php';
        $this->assertEquals(
            'Hello world!',
            ob_get_contents(),
            "Uri $uri should route to helloWorld()"
        );
    }

    public function testComposerStyle()
    {
        $uri = '/hello-world';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        include INROUTE_EXAMPLE_DIR . '/composer.php';
        $this->assertEquals(
            'Hello world!',
            ob_get_contents(),
            "Uri $uri should route to helloWorld()"
        );
    }

    public function testPharStyle()
    {
        $uri = '/hello-world';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        define('INROUTE_SKIP_COMMAND', true);
        include INROUTE_EXAMPLE_DIR . '/phar.php';
        $this->assertEquals(
            'Hello world!',
            ob_get_contents(),
            "Uri $uri should route to helloWorld()"
        );
    }

    public function testMultipleRoutes()
    {
        $uri = '/postAndGet';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        include INROUTE_EXAMPLE_DIR . '/composer.php';
        $this->assertEquals(
            'postAndGet',
            ob_get_contents(),
            "Uri $uri should route to postAndGet()"
        );
    }
    */
}
