<?php
require_once('xts.php');
use xts\Component;
use xts\XComponentFactory;

class MayDay extends Component {
    public $mayday;
    protected static $_conf=array();
    public function __construct($mayday) {
        parent::__construct();
        $this->mayday = $mayday;
    }
    public $_smooth;
    public function setSmooth($var) {
        $this->_smooth = $var;
    }
}

class ComponentTest extends PHPUnit_Framework_TestCase {
    /**
     * @var MayDay
     */
    public $mayday;
    public function setUp() {
        $this->mayday = XComponentFactory::getInstance(
            'MayDay',
            array('help!!'),
            array('abc' => 'aa')
        );
    }
    public function testBug49() {
        $this->assertEquals('help!!', $this->mayday->mayday, 'Bug#49 test failed, expecating string '.
            gettype($this->mayday->mayday). ' present.'
        );
    }

    public function testConfKey() {
        $this->assertArrayHasKey('abc', $this->mayday->conf);
    }

    /**
     * @depends testConfKey
     */
    public function testConfValue() {
        $this->assertEquals('aa', $this->mayday->conf['abc']);
    }

    public function testGetUnsetProp() {
        $this->assertNull($this->mayday->test);
    }

    public function testSetter() {
        $this->mayday->smooth = "abc";
        $this->assertEquals("abc", $this->mayday->_smooth);
    }

    /**
     * @depends testGetUnsetProp
     */
    public function testNoSetter() {
        $this->mayday->test = 'test';
        $this->assertEquals('test', $this->mayday->test);
    }

    public function testIsSet() {
        $this->mayday->test = "test";
        $this->assertTrue(isset($this->mayday->conf));
        $this->assertTrue(isset($this->mayday->test));
        $this->assertFalse(isset($this->mayday->verySmooth));
    }
}
