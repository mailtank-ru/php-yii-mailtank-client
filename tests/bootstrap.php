<?php
require __DIR__ . "/../../../../vendor/autoload.php";


// change the following paths if necessary
$yiit = dirname(__FILE__) . '/../../../../lib/yii/framework/yiit.php';

require($yiit);
$config = include dirname(__FILE__) . '/../../../config/test.php';
$params = include dirname(__FILE__) . '/params.php';
$config['components']['mailtank']['host'] = $params['host'];
$config['components']['mailtank']['token'] = $params['token'];

define('TEST_DIR', __DIR__);

Yii::createWebApplication($config);

require_once(__DIR__ . '/Mailtank_TestCase.php');