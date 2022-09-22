<?php

namespace src\helpers;

use frontend\helpers\RedisHelper;
use src\helpers\text\HashHelper;

class CheckRequestDuplicate
{
    /**
     * @return bool
     */
    public static function isDuplicate(): bool
    {
        return RedisHelper::checkDuplicate(static::getRequestHash(), 2);
    }

    /**
     * @return string
     */
    protected static function getRequestHash(): string
    {
        $request = \Yii::$app->request;

        $requestData = $request->getIsPost() ? $request->post() : $request->get();
        $requestData = array_filter($requestData, function ($key) {
            return !in_array($key, [
                '_csrf-frontend',
            ]);
        }, ARRAY_FILTER_USE_KEY);
        $requestData['url'] = $request->url;
        $requestData['user_ip'] = $request->getUserIP();

        return HashHelper::generateHashFromArray($requestData);
    }
}
