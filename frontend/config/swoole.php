<?php

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/../../common/config/main.php',
    require __DIR__ . '/../../common/config/main-local.php',
    require __DIR__ . '/../config/main.php',
    require __DIR__ . '/../config/main-local.php'
);

$config['components']['response']['class'] = swoole\foundation\web\Response::class;
$config['components']['request']['class'] = swoole\foundation\web\Request::class;
$config['components']['errorHandler']['class'] = swoole\foundation\web\ErrorHandler::class;

return $config;