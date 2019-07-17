<?php

namespace sales\repositories\call;

use common\models\Call;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

class CallRepository
{

    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $callSid
     * @return Call|null
     */
    public function getLastCallByUserCreated(string $callSid): ?Call
    {
        return Call::find()
            ->where(['c_call_sid' => $callSid])
            //->andWhere(['c_call_status' => Call::CALL_STATUS_COMPLETED])
            ->andWhere(['>', 'c_created_user_id', 0])
            ->orderBy(['c_updated_dt' => SORT_DESC])->limit(1)->one();
    }

    /**
     * @param string $callSid
     * @return Call|null
     */
    public function getFirstCall(string $callSid): ?Call
    {
        return Call::find()->where(['c_call_sid' => $callSid])->orderBy(['c_id' => SORT_ASC])->limit(1)->one();
    }

    /**
     * @param $id
     * @return Call
     */
    public function get($id): Call
    {
        if ($call = Call::findOne($id)) {
            return $call;
        }
        throw new NotFoundException('Call is not found');
    }

    /**
     * @param Call $call
     * @return int
     */
    public function save(Call $call): int
    {
        if (!$call->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($call->releaseEvents());
        return $call->c_id;
    }

    /**
     * @param Call $call
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(Call $call): void
    {
        if (!$call->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($call->releaseEvents());
    }

}