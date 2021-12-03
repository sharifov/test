<?php

namespace common\components\jobs;

use sales\model\clientData\entity\ClientDataQuery;
use sales\model\clientDataKey\entity\ClientDataKeyDictionary;
use sales\model\clientDataKey\service\ClientDataKeyService;
use yii\queue\JobInterface;

/**
 * Class CallOutEndedJob
 * @package common\components\jobs
 *
 * @property int $clientId
 */
class CallOutEndedJob extends BaseJob implements JobInterface
{
    public int $clientId;

    public function __construct(int $clientId, ?float $timeStart = null, $config = [])
    {
        $this->clientId = $clientId;
        parent::__construct($timeStart, $config);
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $keyId = ClientDataKeyService::getIdByKeyCache(ClientDataKeyDictionary::APP_CALL_OUT_TOTAL_COUNT);
        if ($keyId) {
            \Yii::info('increment', 'info\increment');
            ClientDataQuery::createOrIncrementValue($this->clientId, $keyId, new \DateTimeImmutable());
        }
    }
}
