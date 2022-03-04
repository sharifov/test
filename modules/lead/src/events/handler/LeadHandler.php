<?php

namespace modules\lead\src\events\handler;

use yii\helpers\VarDumper;

class LeadHandler
{
    public const CLOSE = 'close';

    public function close(?array $params = []): string
    {
        VarDumper::dump($params, 10, true);
        return 'Close Test';
    }
}
