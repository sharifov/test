<?php

namespace src\helpers;

use frontend\helpers\RedisHelper;
use src\helpers\text\HashHelper;

class CheckRequestDuplicate
{
    public const TIMEOUT = 2;

    /**
     * @return bool
     */
    public static function isDuplicate(): bool
    {
        return RedisHelper::checkDuplicate(static::getRequestHash(), static::TIMEOUT);
    }

    /**
     * @return string
     */
    protected static function getRequestHash(): string
    {
        $request = \Yii::$app->request;

        $requestData = $request->getIsPost() ? $request->post() : $request->get();
        $requestData['url'] = $request->url;
        $requestData['user_ip'] = $request->getUserIP();

        return HashHelper::generateHashFromArray($requestData);
    }
}
