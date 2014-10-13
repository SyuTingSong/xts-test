<?php
require_once('xts.php');
use xts\Orange;

class OrangeTest extends PHPUnit_Framework_TestCase {
    public function setUp() {
        if(!X::db()->pdo) {
            $this->markTestSkipped("Skip testing orange since cannot connect to database server");
        }
    }

    public function testNormalSave() {
        if($user = X::orange('user')->one('`webpass_id`=:wid', array(
            ':wid' => sha1('18688127027')
        )))
        $user->remove();

        $o = X::orange('user');
        $mobile = '18688127027';
        $o->webpass_id = sha1($mobile);
        $o->mobile = $mobile;
        $o->save();
        $this->assertGreaterThan(0, $o->id, 'normal save fail');
    }

    /**
     * @depends testNormalSave
     */
    public function testIgnoreSave() {
        $o = X::orange('user');
        $mobile = '18688127027';
        $o->webpass_id = sha1($mobile);
        $o->mobile = $mobile;
        $o->imsi = '46001';
        $o->save(Orange::INSERT_IGNORE);

        $this->assertEquals(0, $o->id);

        $user = X::orange('user')->one('`webpass_id`=:wid', array(
            ':wid' => sha1('18688127027')
        ));

        $this->assertInstanceOf('xts\Orange', $user, '$user is not instance of Orange');
        $this->assertEmpty($user->imsi, 'Ignore failed');
    }

    /**
     * @depends testNormalSave
     */
    public function testUpdateSave() {
        $o = X::orange('user');
        $o->webpass_id = sha1('18688127027');
        $o->imsi = '46001';
        $o->save(Orange::INSERT_UPDATE);

        $this->assertGreaterThan(0, $o->id, 'insert update failed');

        $user = X::orange('user')->one('`webpass_id`=:wid', array(
            ':wid' => sha1('18688127027')
        ));

        $this->assertInstanceOf('xts\Orange', $user, '$user is not instance of Orange');
        $this->assertNotEmpty($user->imsi, 'update failed');
    }

    /**
     * @depends testNormalSave
     * @expectedException \xts\MethodNotImplementException
     */
    public function testReplaceSave() {
        $o = X::orange('user');
        $o->webpass_id = sha1('18688127027');
        $o->save(Orange::INSERT_REPLACE);
    }

    public function testF77() {
        $user = X::orange('user');
        $count = $user->count();
        $this->assertGreaterThan(0, $count);
        $users = $user->many();
        $this->assertEquals($count, count($users));
        $users = $user->all();
        $this->assertEquals($count, count($users));
    }
    public function testF77Cache() {
        $user = X::orange('user');
        $count = $user->cache()->count();
        $this->assertGreaterThan(0, $count);
        $users = $user->cache()->many();
        $this->assertEquals($count, count($users));
        $users = $user->cache()->all();
        $this->assertEquals($count, count($users));
    }
    public function testF76RenameType() {
        $user = X::orange('user');
        $this->assertEquals('user', $user->modelName);
    }

    public function testF80CJSON() {
        $user = X::orange('user')->load(29);
        echo CJSON::encode($user);
        $this->expectOutputRegex('/^\{"id":29,/');
    }

    public function testF81ModelDir() {
        X::orange('data_plan');
        $this->assertTrue(class_exists('DataPlan'));
    }

    public function testAssign() {
        $user = X::orange('user')->assign(array(
            'mobile' => '18688127027',
            'webpass_id' => sha1('18688127027'),
            'imsi' => '46001',
        ));
        $this->assertInstanceOf('\xts\Orange', $user);
        $this->assertEquals('18688127027', $user->mobile);
    }

    public function testRelation() {
        /** @var Orange $order */
        $order = X::orange('order')->load(26);
        $this->assertArrayHasKey('user', $order->relations);
    }

    public function testBelongToRelation() {
        $order = X::orange('order')->load(26);
        $this->assertEquals('user', $order->user->modelName);
        $this->assertEquals(29, $order->user->id);
    }

    public function testBelongToByMethod() {
        $order = X::orange('order')->load(26);
        $this->assertEquals('user', $order->user()->modelName);
    }

    public function testHasManyRelation() {
        $user = X::orange('user')->load(29);
        $orders = $user->orders();
        $this->assertTrue(is_array($orders));
        $order = reset($orders);
        $this->assertEquals('order', $order->modelName);
    }

    public function testHasManyRelation2() {
        $user = X::orange('user')->load(29);
        $this->getActualOutput();
        $orders = $user->orders('data_plan_id is not null');
        $this->assertTrue(is_array($orders));
        $order = reset($orders);
        $this->assertEquals('order', $order->modelName);
    }

    public function testCacheLoad() {
        X::orange('user')->cache(1)->load(29);
        $u = X::cache()->get(md5("load|user|29"));
        $this->assertEquals('user', $u->modelName);
    }

    public function testCacheLoad2() {
        X::orange('user')->cache(1, null, "top-user")->load(29);
        $u = X::cache()->get("top-user");
        $this->assertEquals('user', $u->modelName);
    }

    /**
     * @depends testCacheLoad
     */
    public function testCacheDependency() {
        $user = X::orange('user')->cache(1, function(Orange $user) {
            return $user->modelName == 'user';
        })->load(29);
        $this->assertEquals(29, $user->id);
    }

    public function testPropertyNotInDB() {
        $user = X::orange('user')->load(29);
        $this->assertNull($user->fm365);
    }

    public function testLoadEmpty() {
        $this->assertNull(X::orange('user')->load(1));
    }
}
