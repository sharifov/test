<?php

namespace sales\repositories\sms;

use common\models\Sms;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class SmsRepository
 */
class SmsRepository
{
    private $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return Sms
     */
    public function find($id): Sms
    {
        if ($sms = Sms::findOne($id)) {
            return $sms;
        }
        throw new NotFoundException('Sms is not found');
    }

    /**
     * @param $sid
     * @return Sms
     */
    public function findBySid($sid): Sms
    {
        if ($sms = Sms::findOne(['s_tw_message_sid' => $sid])) {
            return $sms;
        }
        throw new NotFoundException('Sms is not found');
    }

    /**
     * @param Sms $sms
     * @return int
     */
    public function save(Sms $sms): int
    {
        if (!$sms->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($sms->releaseEvents());
        return $sms->s_id;
    }

    /**
     * @param Sms $sms
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(Sms $sms): void
    {
        if (!$sms->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($sms->releaseEvents());
    }
}
