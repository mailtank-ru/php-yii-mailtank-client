<?php

$yiit = __DIR__ . '/../vendor/yiisoft/yii/framework/yiit.php';

require($yiit);
$params = include __DIR__ . '/params.php';
$config = include __DIR__ . '/config.php';

Yii::createConsoleApplication($config);

require_once(__DIR__ . '/Mailtank_TestCase.php');