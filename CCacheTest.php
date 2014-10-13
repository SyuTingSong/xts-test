<?php
require_once('xts.php');
use xts\CCache;

class CCacheTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var \xts\CCache $c
     */
    public $c;
    public $cacheDir;

    public static function setUpBeforeClass() {
        system('rm -rf runtime/compile_cache');
    }

    public function setUp() {
        CCache::conf(X::$conf['component']['cc']['conf']);
        $this->c = new CCache();
        $this->cacheDir = $this->c->conf['cacheDir'];
    }
    public function testSet() {
        $this->c->set('abc', 'abc', 60);
        $this->assertTrue(is_file($this->cacheDir.'/abc.php'));
    }
    /**
     * @depends testSet
     */
    public function testGet() {
        $this->assertEquals('abc', $this->c->get('abc'));
    }
    /**
     * @depends testGet
     */
    public function testRemove() {
        $this->c->remove('abc');
        $this->assertFalse(is_file($this->cacheDir.'/abc.php'));
    }

    /**
     * @depends testRemove
     */
    public function testRemove2() {
        $r = $this->c->remove('abc');
        $this->assertFalse($r);
    }

    /**
     * @param $value
     * @dataProvider dataForTestMany
     */
    public function testMany($value) {
        $this->c->set('test', $value, 60);
        $r = $this->c->get('test');
        $this->assertEquals($value, $r);
    }
    public function dataForTestMany() {
        return array(
            array('abc'),
            array(123),
            array(4.2275),
            array(array('abc' => 7)),
        );
    }
    public function testExpire() {
        $this->c->set('exp', 'abc', 2);
        sleep(1);
        $this->assertEquals('abc', $this->c->get('exp'));
        sleep(1);
        $this->assertEquals('abc', $this->c->get('exp'));
        sleep(1);
        $this->assertFalse($this->c->get('exp'));
    }
    public function testZeroExpire() {
        $this->c->set('exp0', 'bac', 0);
        sleep(1);
        $this->assertEquals('bac', $this->c->get('exp0'));
    }
}
