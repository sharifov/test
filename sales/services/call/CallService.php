<?php

namespace sales\services\call;

use common\models\Call;
use sales\repositories\call\CallRepository;

/**
 * Class CallService
 *
 * @property  CallRepository $callRepository
 */
class CallService
{
    private $callRepository;

    public function __construct(CallRepository $callRepository)
    {
        $this->callRepository = $callRepository;
    }

    /**
     * @param int $callId
     * @param int $userId
     */
    public function cancelByCrash(int $callId, int $userId): void
    {
        if (!$call = Call::findOne(['c_id' => $callId])) {
            throw new \DomainException('Call not found');
        }

        if ($call->isEnded()) {
            throw new \DomainException('Cannot cancel call in current status.');
        }

        if (!$call->isOwner($userId)) {
            throw new \DomainException('You are not owner this call.');
        }

        $call->cancel();
        $this->callRepository->save($call);
    }
}
