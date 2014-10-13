<?php
require_once('xts.php');

class ViewTest extends PHPUnit_Framework_TestCase {

    public static function setUpBeforeClass() {
        system('rm -rf '.X_PROJECT_ROOT.'/runtime/template_c');
    }

    public static function tearDownAfterClass() {
        system('rm -rf '.X_PROJECT_ROOT.'/runtime/template_c');
    }

    public function testClip() {
        $html = X::view()->renderFetch('result', array(
            'trophies' => array(
                array(
                    'category' => 'vip',
                    'city' => 'Beijing',
                    'time' => 1406269309,
                    'addr' => 'creepy address',
                ),
                array(
                    'category' => 'vip',
                    'city' => 'Beijing',
                    'time' => 1407269309,
                    'addr' => 'creepy address 2',
                ),
            ),
        ));
        $this->assertEquals('a741ac4a9a9ffb9fc085f3875292e063', md5($html));
    }

    /**
     * @depends testClip
     */
    public function testCached() {
        $html = X::view()->renderFetch('result', array(
            'trophies' => array(
                array(
                    'category' => 'vip',
                    'city' => 'Beijing',
                    'time' => 1406269309,
                    'addr' => 'creepy address',
                ),
                array(
                    'category' => 'vip',
                    'city' => 'Beijing',
                    'time' => 1407269309,
                    'addr' => 'creepy address 2',
                ),
            ),
        ), 'result');
        $this->assertEquals('a741ac4a9a9ffb9fc085f3875292e063', md5($html));
    }

    /**
     * @depends testCached
     */
    function testCachedAgain() {
        $html = X::view()->renderFetch('result', array(
            'trophies' => array(
                array(
                    'category' => 'vip',
                    'city' => 'Beijing',
                    'time' => 1406269309,
                    'addr' => 'creepy address',
                ),
                array(
                    'category' => 'vip',
                    'city' => 'Beijing',
                    'time' => 1407269309,
                    'addr' => 'creepy address 2',
                ),
            ),
        ), 'result');
        $this->assertEquals('a741ac4a9a9ffb9fc085f3875292e063', md5($html));
    }


    /**
     * @depends testClip
     */
    public function testSetPageTitle() {
        X::view()->setPageTitle("Hello")->render('result', array(
            'trophies' => array(
                array(
                    'category' => 'vip',
                    'city' => 'Beijing',
                    'time' => 1406269309,
                    'addr' => 'creepy address',
                ),
                array(
                    'category' => 'vip',
                    'city' => 'Beijing',
                    'time' => 1407269309,
                    'addr' => 'creepy address 2',
                ),
            ),
        ));
        $this->expectOutputRegex('/\<title\>Hello\<\/title\>/');
    }

    public function testClipDefNotClose() {
        X::view()->render('clipdef_not_close');
        $this->expectOutputRegex('#RainTpl_SyntaxException#');
    }
    public function testClipDefCloseTwice() {
        X::view()->render('clipdef_close_twice');
        $this->expectOutputRegex('#RainTpl_SyntaxException#');
    }
    public function testClipWithoutName() {
        X::view()->render('clip_without_name');
        $this->expectOutputRegex('#RainTpl_SyntaxException#');
    }
    public function testClipDefWithoutName() {
        X::view()->render('clipdef_without_name');
        $this->expectOutputRegex('#RainTpl_SyntaxException#');
    }

    public function testB82ClipAfterInclude() {
        X::view()
            ->setLayout('layout_b82')
            ->render('b82', array(
                'to' => 'world'
            ))->setLayout('layout');
        $this->expectOutputRegex('/\<script\>/');
    }

    public function testDisableClip() {
        xts\Hail::conf(array('enable_clip' => false));

        X::view()->render('result', array(
            'trophies' => array(
                array(
                    'category' => 'vip',
                    'city' => 'Beijing',
                    'time' => 1406269309,
                    'addr' => 'creepy address',
                ),
                array(
                    'category' => 'vip',
                    'city' => 'Beijing',
                    'time' => 1407269309,
                    'addr' => 'creepy address 2',
                ),
            ),
        ));

        $this->expectOutputRegex('/\{clip="css"\}/');
    }

    public function testAssign() {
        X::view()->assign('trophies', array(
            array(
                'category' => 'vip',
                'city' => 'Beijing',
                'time' => 1406269309,
                'addr' => 'creepy address',
            ),
            array(
                'category' => 'vip',
                'city' => 'Beijing',
                'time' => 1407269309,
                'addr' => 'creepy address 2',
            ),
        ))->render('result');
        $this->expectOutputRegex('#creepy address 2#');
    }

    public function testNotExistTpl() {
        X::view()->render('void');
        $this->expectOutputRegex('#RainTpl_NotFoundException#');
    }

    public function testFunction() {
        X::view()->render('index', array(
            'to' => 'world',
        ));
        $this->expectOutputRegex('/^orld/');
        $this->expectOutputRegex('/Unallowed syntax/');
    }

    public function testLayout() {
        X::view()->setLayout('layout2');
        $this->assertEquals('layout2', X::view()->getLayout());
        X::view()->setLayout('layout');
    }
}
