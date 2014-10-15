<?php
require_once('xts.php');

class AppleTest extends PHPUnit_Framework_TestCase {
    public function testAction() {
        $this->setUri('/apple');
        X::apple()->run();
        $this->expectOutputString('the apple');
    }

    public function testParam() {
        $this->setUri('/param?p1=3&p2=5')
            ->setGet(array(
                'p1' => 3,
                'p2' => 5,
            ));
        X::apple()->run();
        $this->expectOutputString('3 5 abc');
    }

    public function testActionInDir() {
        $this->setUri('/dir/in_dir');
        X::apple()->run();
        $this->expectOutputString('in dir');
    }

    public function testPositionBasedParam() {
        $this->setUri('/user/LiLei');
        X::apple()->run();
        $this->expectOutputString('LiLei');
    }

    public function testPositionBasedParamPro() {
        $this->setUri('/user/HanMeiMei');
        X::apple()->run();
        $this->expectOutputString('HMM');
    }

    /**
     * @runInSeparateProcess
     */
    public function testMissingParam() {
        ini_set('display_error', 'on');
        $this->setUri('/missing');
        X::apple()->run();
        $this->expectOutputString('missing query parameter req');
    }
    /**
     * @runInSeparateProcess
     */
    public function testInvalidAction() {
        $this->setUri('/wel-dks/c==');
        X::apple()->run();
        $this->expectOutputString('Bad Request');
    }

    /**
     * @expectedException xts\InvalidActionException
     */
    public function testBug69() {
        $this->setUri('/syslog?priority=4&message=what%20the%20fuck')
            ->setGet(array(
                'priority' => '4',
                'message' => 'what the fuck',
            ));
        X::apple()->run();
    }

    /**
     * @runInSeparateProcess
     */
    public function testPositionWithoutParam() {
        $this->setUri('/admin/welcome');
        X::apple()->run();
        $this->expectOutputString('Position based parameter count mismatch the action function');
    }

    public function testJson() {
        X::apple()->jsonEcho('good',array(
            'hello' => 'world',
            'data' => 1,
        ));
        $this->expectOutputString('{"OK":1,"message":"good","data":{"hello":"world","data":1}}');
    }

    /**
     * @runInSeparateProcess
     */
    public function testJsonError() {
        X::apple()->jsonError(30, 'some error', null, 501);
        $headers = xdebug_get_headers();
        $this->assertContains('Status: 501', $headers);
        $this->expectOutputString('{"OK":0,"message":30,"data":"some error"}');
    }

    /**
     * @runInSeparateProcess
     */
    public function testJsonStatus() {
        X::apple()->jsonEcho('good',array(
            'hello' => 'world',
            'data' => 1,
        ), 'http://www.baidu.com/');
        $this->expectOutputString('{"OK":1,"message":"good","data":{"hello":"world","data":1},"goto":"http:\/\/www.baidu.com\/"}');
    }

    public function testJsonOrange() {
        X::apple()->jsonEcho('Success', X::orange('user')->load(29));
        $this->expectOutputRegex('/^\{"OK":1,"message":"Success","data":\{"id":29,/');
    }

    /**
     * @runInSeparateProcess
     */
    public function testNotExistAction() {
        $this->setUri('/not_exists');
        X::apple()->run();
        $this->expectOutputString('404 Not Found');
    }

    /**
     * @runInSeparateProcess
     */
    public function testRedirect() {
        X::apple()->redirect('/login');
        $headers = xdebug_get_headers();
        $this->assertContains('Location: /login', $headers);
    }

    /**
     * @depends testNotExistAction
     */
    public function testFallback() {
        $fallback = <<<'PHPCODE'
        function fallback_action($action) {
            echo $action;
        }
PHPCODE;
        eval($fallback);
        $this->setUri('/not_exists');
        X::apple()->run();
        $this->expectOutputString('/not_exists');
    }

    public function testDefaultAction() {
        $this->setUri('/');
        X::apple()->run();
        $this->expectOutputString('index');
    }

    public function testAppEnd() {
        $this->setUri('/append');
        X::apple()->run();
        $this->expectOutputString('echo');
    }

    public function testCatchException() {
        ini_set('display_errors', false);
        $this->setUri('/syslog?priority=4&message=what%20the%20fuck');
        X::apple()->run();
        $this->expectOutputString('');
    }


    private function setUri($uri) {
        $_SERVER['REQUEST_URI'] = $uri;
        return $this;
    }
    private function setGet($get) {
        $_GET = $get;
        return $this;
    }

    public function testPreAction() {
        $_GET['pre_action_test'] = 1;
        $_SERVER['REQUEST_URI'] = '/index';
        X::apple()->run();
        $this->expectOutputString("pre_action called\nindex");
    }

    public function testErrorPreAction() {
        \xts\Apple::conf(array(
            'actionDir' => X_PROJECT_ROOT.'/action',
            'defaultAction' => '/index',
            'preAction' => '',
            'preActionFile' => '',
        ));
        $_GET['pre_action_test'] = 1;
        $_SERVER['REQUEST_URI'] = '/index';
        X::apple()->run();
        $this->expectOutputString("index");
        //restore the configuration
        \xts\Apple::conf(X::$conf['component']['apple']['conf']);
    }

    public function testActionPrefix() {
        $_SERVER['REQUEST_URI'] = '/prefix';
        X::apple()->run();
        $this->expectOutputString('action_prefix');
    }
}