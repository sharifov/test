<?php

namespace src\services;

use yii\base\Request;
use frontend\helpers\RedisHelper;
use src\helpers\text\HashHelper;

/**
 * @property Request $request
 */
class CheckRequestDuplicateService
{
    /** @var Request  */
    protected Request $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $hash
     * @param int $timeout
     *
     * @return bool
     */
    public function isDuplicate(string $hash, int $timeout = 2): bool
    {
        return RedisHelper::checkDuplicate($hash, $timeout);
    }

    /**
     * @return string
     */
    public function getRequestHash(): string
    {
        $requestData = $this->request->getIsPost() ? $this->request->post() : $this->request->get();
        $requestData['url'] = $this->request->url;
        $requestData['user_ip'] = $this->request->getUserIP();

        return HashHelper::generateHashFromArray($requestData);
    }
}
