<?php
/**
 * Created by PhpStorm.
 * User: rek
 * Date: 2014/8/27
 * Time: 下午12:35
 */

class DataPlan extends \xts\Orange {
    public function __construct() {
        parent::__construct('data_plan');
    }
    public function getTableName() {
        return 'data_plan';
    }
}