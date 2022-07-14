<?php

namespace common\components\jobs;

use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\object\dto\ClientSegmentObjectDto;
use src\helpers\app\AppHelper;
use src\repositories\client\ClientsQuery;
use Yii;
use yii\queue\JobInterface;

class ClientReturnIndicationJob extends BaseJob implements JobInterface
{
    public int $clientId;

    public function __construct(int $clientId, ?float $timeStart = null, $config = [])
    {
        parent::__construct($timeStart, $config);
        $this->clientId = $clientId;
    }

    public function execute($queue)
    {
        try {
            if ($client = ClientsQuery::findById($this->clientId)) {
                $dto = new ClientSegmentObjectDto($client);
                Yii::$app->objectSegment->segment($dto, ObjectSegmentKeyContract::TYPE_KEY_CLIENT);
            }
        } catch (\Throwable $e) {
            Yii::error(AppHelper::throwableLog($e, true), 'ClientReturnIndicationJob::objectSegment');
        }
    }
}
