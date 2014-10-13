<?php
require_once('xts.php');
/**
 * @requires extension memcache
 */
class MCacheTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var xts\MCache $c
     */
    public $c;
    public function setUp() {
        $this->c = X::cache();
    }

    public function testRemove() {
        $this->c->set('abc', 'abc', 60);
        $this->c->remove('abc');
        $this->assertFalse($this->c->get('abc'));
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
        usleep(950);
        $this->assertEquals('abc', $this->c->get('exp'));
        sleep(1);
        $this->assertFalse($this->c->get('exp'));
    }

    public function testFlush() {
        $this->c->set('flush', 'abc', 0);
        $r = $this->c->get('flush');
        $this->assertNotFalse($r);
        $this->c->flush();
        $r = $this->c->get('flush1');
        $this->assertFalse($r);
    }

    public function testInc() {
        $this->c->set('inc1', 1, 0);
        $r1 = $this->c->inc('inc1');
        $this->assertEquals($r1, 2);
        $r2 = $this->c->get('inc1');
        $this->assertEquals($r2, 2);
    }

    public function testIncBy() {
        $this->c->set('inc1', 1, 0);
        $r1 = $this->c->inc('inc1', 2);
        $this->assertEquals($r1, 3);
        $r2 = $this->c->get('inc1');
        $this->assertEquals($r2, 3);
    }

    public function testDec() {
        $this->c->set('dec1', 5, 0);
        $r1 = $this->c->dec('dec1');
        $this->assertEquals($r1, 4);
        $r2 = $this->c->get('dec1');
        $this->assertEquals($r2, 4);
    }

    public function testDecBy() {
        $this->c->set('dec1', 5, 0);
        $r1 = $this->c->dec('dec1', 3);
        $this->assertEquals($r1, 2);
        $r2 = $this->c->get('dec1');
        $this->assertEquals($r2, 2);
    }

    public function testDisabledPersistentGetCache() {
        \xts\MCache::conf(array('persistent' => false));
        $m = new xts\MCache();
        $this->assertInstanceOf('Memcache', $m->getCache());
    }
}
