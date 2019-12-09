<?php

namespace sales\services\call;

use common\models\Call;
use sales\repositories\call\CallRepository;
use sales\services\ServiceFinder;

/**
 * Class CallService
 *
 * @property  CallRepository $callRepository
 * @property  ServiceFinder $finder
 */
class CallService
{
    private $callRepository;
    private $finder;

    public function __construct(CallRepository $callRepository, ServiceFinder $finder)
    {
        $this->callRepository = $callRepository;
        $this->finder = $finder;
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

    /**
     * @param int|Call $call
     */
    public function declined($call): void
    {
        $call = $this->finder->callFind($call);
        $call->declined();
        $this->callRepository->save($call);
    }
}
