<?php

namespace modules\eventManager\src;

class EventHandler
{

    /**
     * @param $params
     */
    public static function handler($params): void
    {
        $data = $params->data;
        \Yii::info(['data' => $data], 'info\EventHandler');
    }
}
