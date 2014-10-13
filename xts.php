<?php
/**
 * Created by PhpStorm.
 * User: rek
 * Date: 6/23/14
 * Time: 12:35 PM
 */
defined('X_DEBUG') or define('X_DEBUG', true);
define('X_PROJECT_ROOT', __DIR__);
define('X_RUNTIME_ROOT', __DIR__.'/runtime');
date_default_timezone_set('Asia/Shanghai');
require_once('xts/x.php');

/**
 * Class X
 * @method static \xts\View view()
 * @method static \xts\Redis redis()
 * @method static \xts\MySmarty smarty()
 * @method static \xts\Valley valley()
 */
class X extends \xts\XComponentFactory {
    public static $conf;
    public static function assignConf() {
        self::$conf =& parent::$_conf;
    }
}
X::conf(require(X_PROJECT_ROOT.'/config/debug.php'));

X::assignConf();
