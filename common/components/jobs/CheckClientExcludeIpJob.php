<?php

namespace common\components\jobs;

use src\model\client\useCase\excludeInfo\ClientExcludeIpChecker;
use yii\queue\JobInterface;

/**
 * Class CheckClientExcludeIpJob
 *
 * @property int $clientId
 * @property string $ip
 */
class CheckClientExcludeIpJob extends BaseJob implements JobInterface
{
    public $clientId;
    public $ip;

    public function __construct(int $clientId, string $ip)
    {
        $this->clientId = $clientId;
        $this->ip = $ip;
        parent::__construct();
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        try {
            $checker = \Yii::createObject(ClientExcludeIpChecker::class);
            $checker->check($this->clientId, $this->ip);
        } catch (\RuntimeException | \DomainException $e) {
            \Yii::warning([
                'message' => $e->getMessage(),
                'clientId' => $this->clientId,
                'ip' => $this->ip,
            ], 'CheckClientExcludeIpJob');
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'clientId' => $this->clientId,
                'ip' => $this->ip,
            ], 'CheckClientExcludeIpJob');
        }
    }
}
