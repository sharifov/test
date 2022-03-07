<?php

namespace modules\lead\src\events\handler;

use yii\helpers\VarDumper;

class LeadHandler
{
    public const CLOSE = 'close';

    public function close(?array $eventData = [], ?array $eventParams = [], ?array $handlerParams = []): string
    {
        VarDumper::dump($eventData, 10, true);
        VarDumper::dump($eventParams, 10, true);
        VarDumper::dump($handlerParams, 10, true);

        return 'Close Test';
    }
}
