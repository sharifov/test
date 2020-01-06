<?php

namespace webapi\src\logger\behaviors;

use webapi\src\behaviors\BaseBehavior;
use Yii;
use webapi\src\logger\ApiLogger;

class LoggerBehavior extends BaseBehavior
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
