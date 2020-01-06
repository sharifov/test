<?php

namespace webapi\src\behaviors;

use Yii;
use webapi\src\logger\ApiLogger;
use yii\base\Behavior;

class LoggerBehavior extends Behavior
{
    protected function checkLogger($controller): ?ApiLogger
    {
        $logger = $controller->logger ?? null;
        if (!$logger instanceof ApiLogger) {
            $logger = null;
            Yii::error('Controller: ' . $controller->id . '. Not found ApiLogger.', 'SimpleLoggerBehavior');
        }
        return $logger;
    }
}
