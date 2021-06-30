<?php

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'loc');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

if (isset($config['components']['cache']['class'])) {
    $config['components']['cache']['class'] = 'yii\caching\DummyCache';
}
if (isset($config['components']['cache']['redis'])) {
    unset($config['components']['cache']['redis']);
}

if (isset($config['bootstrap'])) {
    unset($config['bootstrap']);
}

(new yii\web\Application($config))->run();
