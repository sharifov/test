<?php

namespace sales\model\leadRedial\job;

use common\components\jobs\BaseJob;
use common\models\Call;
use sales\model\user\entity\userStatus\UserStatus;
use yii\queue\JobInterface;

/**
 * Class CheckUserIsOnRedialCallJob
 *
 * @property int $userId
 * @property int $leadId
 * @property int $createdDt
 */
class CheckUserIsOnRedialCallJob extends BaseJob implements JobInterface
{
    public int $userId;
    public int $leadId;
    public string $createdDt;

    public function __construct(int $userId, int $leadId, string $createdDt, ?float $timeStart = null, $config = [])
    {
        parent::__construct($timeStart, $config);
        $this->userId = $userId;
        $this->leadId = $leadId;
        $this->createdDt = $createdDt;
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();

        $isExistRedialCall = Call::find()
            ->andWhere([
                'c_call_type_id' => Call::CALL_TYPE_OUT,
                'c_source_type_id' => Call::SOURCE_REDIAL_CALL,
                'c_created_user_id' => $this->userId,
                'c_lead_id' => $this->leadId,
            ])
            ->andWhere(['>', 'c_created_dt', $this->createdDt])
            ->exists();

        if ($isExistRedialCall) {
            return;
        }

        UserStatus::isOnCallOff($this->userId);
    }
}
