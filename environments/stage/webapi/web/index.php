<?php

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../common/bootstrap/EnvLoader.php';
$dotenv = (new \common\bootstrap\EnvLoader(__DIR__ . '/../../'))->load();
$dotenv->validate();

require __DIR__ . '/../../common/helpers/EnvHelper.php';
defined('YII_DEBUG') or define('YII_DEBUG', env('YII_DEBUG', 'bool', false));
defined('YII_ENV') or define('YII_ENV', env('YII_ENV', 'str', 'stage'));

require __DIR__ . '/../../vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/../../common/config/bootstrap.php';
require __DIR__ . '/../config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

(new yii\web\Application($config))->run();
