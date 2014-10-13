<?php
require_once('xts.php');
require_once('xts/valley.php');

class ValleyTest extends PHPUnit_Framework_TestCase {

    public function testNotString() {
        $v = new xts\ValleyString(35);
        $v->startValidate();
        $this->assertFalse($v->isValid);
    }

    /**
     * @dataProvider lengthDataProvider
     * @param $min
     * @param $max
     * @param $str
     * @param $isValid
     */
    public function testLength($str, $min, $max, $isValid) {
        $validator = new xts\ValleyString($str);
        $validator->length($min, $max)->startValidate();
        $this->assertEquals($isValid, $validator->isValid);
    }

    public function lengthDataProvider() {
        return array(
            array('abc', 3, 3, true),
            array('abc', 2, 4, true),
            array('abc', 4, 9, false),
            array('abc', 0, 2, false),
            array('abc', 5, null, false),
            array('abc', 1, null, true),
        );
    }

    /**
     * @dataProvider containsDataProvider
     * @param $str
     * @param $needle
     * @param $isValid
     * @param $ci
     */
    public function testContains($str, $needle, $ci, $isValid) {
        $v = new \xts\ValleyString($str);
        if($ci) {
            $v->containCI($needle);
        } else {
            $v->contain($needle);
        }
        $v->startValidate();
        $this->assertEquals($isValid, $v->isValid);
    }

    public function containsDataProvider() {
        return array(
            array('I am going to school', 'going', false, true),
            array('I am going to school', 'Going', false, false),
            array('I am going to school', 'going', true, true),
            array('I am going to school', 'Going', true, true),
            array('I am going to school', 'goes', false, false),
            array('I am going to school', 'goeS', true, false),
        );
    }

    /**
     * @dataProvider startWithDataProvider
     * @param $str
     * @param $needle
     * @param $ci
     * @param $isValid
     */
    public function testStartWith($str, $needle, $ci, $isValid) {
        $v = new \xts\ValleyString($str);
        if($ci)
            $v->startWithCI($needle);
        else
            $v->startWith($needle);
        $v->startValidate();
        $this->assertEquals($isValid, $v->isValid);
    }
    public function startWithDataProvider() {
        return array(
            array('I am a student', 'I', true, true),
            array('I am a student', 'I', false, true),
            array('I am a student', 'i', true, true),
            array('I am a student', 'i', false, false),
            array('I am a student', 'I am', true, true),
            array('I am a student', 'I am', false, true),
            array('I am a student', 'i am', true, true),
            array('I am a student', 'I Am', false, false),
            array('I am a student', 'student', true, false),
            array('I am a student', 'student', false, false),
        );
    }

    /**
     * @dataProvider endWithDataProvider
     * @param $str
     * @param $needle
     * @param $ci
     * @param $isValid
     */
    public function testEndWith($str, $needle, $ci, $isValid) {
        $v = new \xts\ValleyString($str);
        if($ci)
            $v->endWithCI($needle);
        else
            $v->endWith($needle);
        $v->startValidate();
        $this->assertEquals($isValid, $v->isValid);
    }
    public function endWithDataProvider() {
        return array(
            array('I am a teacher', 'er', true, true),
            array('I am a teacher', 'er', false, true),
            array('I am a teacher', 'Er', true, true),
            array('I am a teacher', 'Er', false, false),
            array('I am a teacher', 'teacher', true, true),
            array('I am a teacher', 'teacher', false, true),
            array('I am a teacher', 'tea', false, false),
            array('I am a teacher', 'tea', true, false),
            array('I am a teacher', 'tae', true, false),
        );
    }

    public function testInEnum() {
        $v = new xts\ValleyString('abc');
        $v->inEnum(array(
            'abc', 'def', 'ghi'
        ));
        $v->startValidate();
        $this->assertTrue($v->isValid);
    }

    public function testNotInEnum() {
        $v = new xts\ValleyString('abc');
        $v->inEnum(array('att', 'btt'))->startValidate();
        $this->assertFalse($v->isValid);
    }

    public function testInEmptyEnum() {
        $v = new \xts\ValleyString('abc');
        $v->inEnum(array())->startValidate();
        $this->assertFalse($v->isValid);
    }

    public function testMatch() {
        $v = new \xts\ValleyString('abc');
        $v->match('/^\w*$/')->startValidate();
        $this->assertTrue($v->isValid);

        $v = new \xts\ValleyString('abc');
        $v->match('/^\d*$/')->startValidate();
        $this->assertFalse($v->isValid);
    }

    public function testNotMatch() {
        $v = new \xts\ValleyString('abc');
        $v->notMatch('/^\d*$/')->startValidate();
        $this->assertTrue($v->isValid);

        $v = new \xts\ValleyString('abc');
        $v->notMatch('/^\w*$/')->startValidate();
        $this->assertFalse($v->isValid);
    }

    public function testCallback() {
        $v = new \xts\ValleyString('abc');
        $v->callback(function($var) {
            return $var == 'abc';
        })->startValidate();
        $this->assertTrue($v->isValid);

        $v = new \xts\ValleyString('def');
        $v->callback(function($var) {
            return $var == 'abc';
        })->startValidate();
        $this->assertFalse($v->isValid);
    }

    public function testOnErrorSet() {
        $v = new \xts\ValleyString('abc');
        $v->startWith('def')->onErrorSet('hehe')->startValidate();
        $this->assertTrue($v->isValid);
        $this->assertEquals('hehe', $v->safeVar);
    }

    public function testOnEmptySet() {
        $v = new \xts\ValleyString('');
        $v->onEmptySet('hehe')->startValidate();
        $this->assertTrue($v->isValid);
        $this->assertEquals('hehe', $v->safeVar);
    }

    public function testOnErrorReport() {
        $v = new \xts\ValleyString('abc');
        $v->startWith('def')->onErrorReport('hehe')->startValidate();
        $this->assertContains('hehe', $v->messages);
    }

    public function testOnEmptyReport() {
        $v = new \xts\ValleyString('');
        $v->startWith('abc')
            ->onEmptyReport('hehe')
            ->onErrorReport('hehe2')
            ->startValidate();
        $this->assertContains('hehe', $v->messages);
    }

    public function testIsEmpty() {
        $v = new \xts\ValleyString('');
        $v->startWith('abc')->startValidate();
        $this->assertTrue($v->isEmpty);
    }

    /**
     * @dataProvider emailDataProvider
     * @param $email
     * @param $expect
     */
    public function testEmail($email, $expect) {
        $v = new \xts\ValleyEmail($email);
        $v->startValidate();
        $this->assertEquals($expect, $v->isValid);
    }
    public function emailDataProvider() {
        return array(
            array('abc@example.com', true),
            array('abc@abc', false),
            array('@com', false),
            array('foobar', false),
            array('a.b.c@abc.com', true),
            array('a_b@example.com', true),
            array('a-b@example.com', true),
            array('-bb@ac.com', true),
            array('aa@-ac.com', false),
            array('bb@ac.com-', false),
        );
    }

    /**
     * @dataProvider dateDataProvider
     * @param $date
     * @param $expect
     */
    public function testDate($date, $expect) {
        $v = new \xts\ValleyDate($date);
        $v->startValidate();
        $this->assertEquals($expect, $v->isValid);
    }
    public function dateDataProvider() {
        return array(
            array('2014-06-14', true),
            array('2014-06-41', false),
            array('01-06-02', true),
            array('2012-02-29', true),
            array('2009-02-29', false),
            array('2014/06/14', true),
            array('Thu, 21 Aug 2014 13:03:21 GMT', true),
        );
    }

    /**
     * @dataProvider urlDataProvider
     * @param $url
     * @param $expect
     */
    public function testUrl($url, $expect) {
        $v = new \xts\ValleyUrl($url);
        $v->startValidate();
        $this->assertEquals($expect, $v->isValid);
    }
    public function urlDataProvider() {
        return array(
            array('http://www.oupeng.com/', true),
            array('ftp://ftp.oupeng.com/', true),
            array('https://www.oupeng.com/', true),
            array('http://t.sh/index?page=1&group_id=7&q=%3E%2B', true),
            array('file:///var/htdocs/index.html', true),
            array('http://t.sh/中文', false),
            array('http://t.sh/%E4%B8%AD%E6%96%87', true),
        );
    }

    /**
     * @dataProvider telDataProvider
     * @param $tel
     * @param $expect
     */
    public function testTel($tel, $expect) {
        $v = new \xts\ValleyTel($tel);
        $v->startValidate();
        $this->assertEquals($expect, $v->isValid);
    }
    public function telDataProvider() {
        return array(
            array('13800138000', true),
            array('010-62258810-0-2', true),
            array('+86-135-8808-2947', true),
            array('+86 10) 67714428', true),
            array('BAc', false),
        );
    }

    /**
     * @dataProvider numDataProvider
     * @param $num
     * @param $expect
     */
    public function testNum($num, $expect) {
        $v = new \xts\ValleyNumber($num);
        $v->startValidate();
        $this->assertEquals($expect, $v->isValid);
    }

    public function numDataProvider() {
        return array(
            array(3.14, true),
            array('3.14', true),
            array(3, true),
            array('3', true),
            array(0x25, true),
            array('0x25', true),
            array('ABC', true),
            array('Welcome', false),
        );
    }

    /**
     * @dataProvider gtDP
     * @param $num
     * @param $than
     * @param $orEqual
     * @param $expect
     */
    public function testGreaterThan($num, $than, $orEqual, $expect) {
        $v = new \xts\ValleyNumber($num);
        $v->gt($than, $orEqual)->startValidate();
        $this->assertEquals($expect, $v->isValid);
    }

    public function gtDP() {
        return array(
            array(3.14, 1, false, true),
            array(3.14, 4, false, false),
            array(3.14, 3.14, true, true),
            array(3.14, 3.14, false, false),
            array('3.14', 1, false, true),
            array('3.14', 4, false, false),
            array('3.14', 3.14, true, true),
            array('3.14', 3.14, false, false),
        );
    }

    /**
     * @dataProvider ltDP
     * @param $num
     * @param $than
     * @param $orEqual
     * @param $expect
     */
    public function testLessThan($num, $than, $orEqual, $expect) {
        $v = new \xts\ValleyNumber($num);
        $v->lt($than, $orEqual)->startValidate();
        $this->assertEquals($expect, $v->isValid);
    }
    public function ltDP() {
        return array(
            array(0.618, 1, false, true),
            array(0.618, 0, false, false),
            array(0.618, 0.618, true, true),
            array(0.618, 0.618, false, false),
            array('0.618', 1, false, true),
            array('0.618', 0, false, false),
            array('0.618', 0.618, true, true),
            array('0.618', 0.618, false, false),
        );
    }

    public function testEqual() {
        $v = new \xts\ValleyNumber(1.41421);
        $v->eq(1.41421)->startValidate();
        $this->assertTrue($v->isValid);

        $v = new \xts\ValleyNumber(1.41421);
        $v->eq(1.41420)->startValidate();
        $this->assertFalse($v->isValid);
    }

    public function testNotEqual() {
        $v = new \xts\ValleyNumber(1.41421);
        $v->ne(1.41420)->startValidate();
        $this->assertTrue($v->isValid);

        $v = new \xts\ValleyNumber(1.41421);
        $v->ne(1.41421)->startValidate();
        $this->assertFalse($v->isValid);
    }

    /**
     * @dataProvider betweenDP
     * @param $num
     * @param $min
     * @param $max
     * @param $includeMin
     * @param $includeMax
     * @param $expect
     */
    public function testBetween($num, $min, $max, $includeMin, $includeMax, $expect) {
        $v = new \xts\ValleyNumber($num);
        $v->between($min, $max, $includeMin, $includeMax)->startValidate();
        $this->assertEquals($expect, $v->isValid);
    }
    public function betweenDP() {
        return array(
            array(30, 15, 20, false, false, false),
            array(30, 15, 40, false, false, true),
            array(30, 40, 60, false, false, false),
            array(30, 30, 40, false, false, false),
            array(30, 30, 40, true, false, true),
            array(30, 20, 30, false, false, false),
            array(30, 20, 30, true, false, false),
            array(30, 20, 30, false, true, true),
            array(30, 30, 30, true, true, true),
            array(30, 30, 30, true, false, false),
            array(30, 40, 15, true, false, false),
        );
    }

    public function testNumInEnum() {
        $v = new \xts\ValleyNumber(8);
        $v->inEnum(array(1,2,3,4))->startValidate();
        $this->assertFalse($v->isValid);

        $v = new \xts\ValleyNumber('8');
        $v->inEnum(array(1,2,3,4))->startValidate();
        $this->assertFalse($v->isValid);

        $v = new \xts\ValleyNumber(8);
        $v->inEnum(array(5,6,7,8))->startValidate();
        $this->assertTrue($v->isValid);

        $v = new \xts\ValleyNumber('8');
        $v->inEnum(array(5,6,7,8))->startValidate();
        $this->assertTrue($v->isValid);
    }

    /**
     * @dataProvider intDP
     * @param $num
     * @param $isInt
     * @param $val
     */
    public function testInteger($num, $isInt, $val) {
        $v = new \xts\ValleyInteger($num);
        $v->startValidate();
        $this->assertEquals($isInt, $v->isValid);
        $this->assertEquals($val, $v->safeVar);
    }
    public function intDP() {
        return array(
            array(3, true, 3),
            array(-3, true, -3),
            array(-3.5, false, null),
            array('3', true, 3),
            array('-3', true, -3),
            array('abcdef', true, 11259375),
            array('0xab', true, 171),
            array('0.5', false, null),
            array('tty', false, null),
        );
    }

    /**
     * @dataProvider decDP
     * @param $num
     * @param $isDec
     * @param $val
     */
    public function testDecimal($num, $isDec, $val) {
        $v = new \xts\ValleyDecimal($num);
        $v->startValidate();
        $this->assertEquals($isDec, $v->isValid);
        $this->assertEquals($val, $v->safeVar);
    }
    public function decDP() {
        return array(
            array(3, true, 3),
            array(-3, true, -3),
            array(-3.5, false, null),
            array('3', true, 3),
            array('-3', true, -3),
            array('abcdef', false, null),
            array('0xab', false, null),
            array('0.5', false, null),
            array('tty', false, null),
        );
    }

    /**
     * @dataProvider hexDP
     * @param $num
     * @param $isHex
     * @param $val
     */
    public function testHexadecimal($num, $isHex, $val) {
        $v = new \xts\ValleyHexadecimal($num);
        $v->startValidate();
        $this->assertEquals($isHex, $v->isValid);
        $this->assertEquals($val, $v->safeVar);
    }
    public function hexDP() {
        return array(
            array(3, true, 3),
            array(-3, true, -3),
            array(-3.5, false, null),
            array('3', true, 3),
            array('-3', false, null),
            array('abcdef', true, 11259375),
            array('0xab', true, 171),
            array('0.5', false, null),
            array('tty', false, null),
        );
    }

    /**
     * @dataProvider fltDP
     * @param $num
     * @param $isValid
     * @param $val
     */
    public function testFloat($num, $isValid, $val) {
        $v = new \xts\ValleyFloat($num);
        $v->startValidate();
        $this->assertEquals($isValid, $v->isValid);
        $this->assertEquals($val, $v->safeVar);
    }
    public function fltDP() {
        return array(
            array(3, true, 3),
            array(-3, true, -3),
            array(-3.5, true, -3.5),
            array('3', true, 3),
            array('-3', true, -3),
            array('abcdef', false, null),
            array('0xab', false, null),
            array('0.5', true, 0.5),
            array('.5', true, 0.5),
            array('tty', false, null),
        );
    }

    /**
     * @dataProvider boolDP
     * @param $var
     * @param $expect
     */
    public function testBoolean($var, $expect) {
        $v = new \xts\ValleyBoolean($var);
        $v->startValidate();
        $this->assertEquals($expect, $v->safeVar);
    }
    public function boolDP() {
        return array(
            array(1, 1),
            array(0, 0),
            array(2, 0),
            array('1', 1),
            array('0', 0),
            array('2', 0),
            array(true, 1),
            array(false, 0),
            array('true', 1),
            array('false', 0),
            array('on', 1),
            array('off', 0),
            array('yes', 1),
            array('no', 0),
            array('failed', 0),
        );
    }

    public function testArray() {
        $v = new \xts\ValleyArray('abc');
        $v->startValidate();
        $this->assertFalse($v->isValid);

        $v = new \xts\ValleyArray(array());
        $v->startValidate();
        $this->assertTrue($v->isValid);
    }

    /**
     * @dataProvider arrDP
     * @param $var
     * @param $min
     * @param $max
     * @param $isValid
     * @param $isEmpty
     */
    public function testArrayLength($var, $min, $max, $isValid, $isEmpty) {
        $v = new \xts\ValleyArray($var);
        $v->length($min, $max)->startValidate();
        $this->assertEquals($isValid, $v->isValid);
        $this->assertEquals($isEmpty, $v->isEmpty);
    }

    public function arrDP() {
        return array(
            array('', 0, null, true, true),
            array('abc', 0, null, false, false),
            array(array(), 0, null, true, true),
            array(array(1), 0, null, true, false),
            array(array(1), 1, null, true, false),
            array(array(1), 2, null, false, false),
            array(array(1,2), 1, 2, true, false),
            array(array(1,2), 2, 2, true, false),
            array(array(1,2), 3, 2, false, false),
            array(array(1,2,3), 3, 2, false, false),
            array(array(1,2,3), 3, 3, true, false),
            array(array(1,2,3), 3, 4, true, false),
            array(array(1,2,3,4), 3, 4, true, false),
        );
    }


    public function testChain() {
        $var = array(
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'age' => 17,
            'blog' => 'http://www.blogger.com/',
            'subscribe' => 'on',
            'list' => array(
                'news' => 'on',
                'bug' => 'on',
            ),
            'list2' => 'None',
            'auto_upgrade' => '1',
            'category' => '2',
            'discount' => '0.85',
            'start_date' => '2014/08/12',
            'mobile' => '13800138000',
            'mask' => '20FF',
            'test' => '3.3',
        );
        $v = X::valley($var)
            ->str('name')->match('/^\w+ ?\w*$/')->onEmptyReport('Name cannot be empty')->onErrorReport('Invalid name')
            ->email('email')->onEmptyReport('Email is empty')->onErrorReport('Email format error')
            ->int('age')->gt(18, true)->onEmptyReport('Age cannot be empty')->onErrorReport('You are too young')
            ->url('blog')
            ->bool('subscribe')
            ->arr('list')->length(1)->onErrorReport('You must choose at least one in list')
            ->arr('list2')
            ->dec('auto_upgrade')->inEnum(array(0,1,2,3))->onErrorSet(0)
            ->float('discount')->between(0, 1)->onErrorReport('discount must between 0 and 1')
            ->date('start_date')->match('/^\d{4}\/\d{1,2}\/\d{1,2}$/')->onErrorReport('Date format error')
            ->tel('mobile')->length(11,11)->onErrorReport('Mobile number must be 11 digits')
            ->hex('mask')->eq(0x20ff)
            ->num('test')
            ->startValidate()
        ;
        $this->assertArrayHasKey('age', $v->messages);
        $this->assertArrayHasKey('list2', $v->messages);
        $this->assertFalse($v->isValid);
    }

    public function testAssignTo() {
        $user = X::orange('user');
        $_POST = array(
            'mobile' => '13581748511',
            'imei' => '862620025106313',
        );
        X::valley($_POST)
            ->tel('mobile')->length(11, 11)
            ->str('imei')->match('/^\d*$/')
            ->startValidate()
            ->assignTo($user);
        $this->assertEquals('13581748511', $user->mobile);
        $this->assertEquals('862620025106313', $user->imei);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testAssignWarning() {
        $user = X::orange('user');
        $_POST = array(
            'mobile' => '13581748511',
            'imei' => '862620025106313',
        );
        X::valley($_POST)
            ->tel('mobile')->length(11, 11)
            ->str('imei')->match('/^\d*$/')
            ->assignTo($user);
    }
}
