<?php

namespace common\components\jobs;

use sales\model\client\useCase\excludeInfo\ClientExcludeIpChecker;
use yii\queue\JobInterface;

/**
 * Class CheckClientExcludeIpJob
 *
 * @property int $clientId
 * @property string $ip
 */
class CheckClientExcludeIpJob implements JobInterface
{
    public $clientId;
    public $ip;

    public function __construct(int $clientId, string $ip)
    {
        $this->clientId = $clientId;
        $this->ip = $ip;
    }

    public function execute($queue)
    {
        try {
            $checker = \Yii::createObject(ClientExcludeIpChecker::class);
            $checker->check($this->clientId, $this->ip);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'clientId' => $this->clientId,
                'ip' => $this->ip,
            ], 'CheckClientExcludeIpJob');
        }
    }
}
