<?php
/**
 * Created by PhpStorm.
 * User: rek
 * Date: 8/23/14
 * Time: 9:40 PM
 */

function pre_action($action) {
    if($_GET['pre_action_test'])
        echo "pre_action called\n";
}