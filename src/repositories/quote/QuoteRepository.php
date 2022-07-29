<?php

namespace src\repositories\quote;

use common\models\Quote;
use src\dispatchers\EventDispatcher;
use src\repositories\NotFoundException;

/**
 * Class QuoteRepository
 */
class QuoteRepository
{
    private $eventDispatcher;

    /**
     * QuoteRepository constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $id
     * @return Quote
     */
    public function find($id): Quote
    {
        if ($quote = Quote::findOne($id)) {
            return $quote;
        }
        throw new NotFoundException('Quote is not found');
    }

    /**
     * @param $uid
     * @return Quote
     */
    public function findByUid($uid): Quote
    {
        if ($quote = Quote::findOne(['uid' => $uid])) {
            return $quote;
        }
        throw new NotFoundException('Quote is not found');
    }

    /**
     * @param Quote $quote
     * @return int
     */
    public function save(Quote $quote): int
    {
        if (!$quote->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($quote->releaseEvents());
        return $quote->id;
    }

    /**
     * @param Quote $quote
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function remove(Quote $quote): void
    {
        if (!$quote->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($quote->releaseEvents());
    }


    public function getAmountQuoteByLeadIdAndStatusesAndCreateTypes(int $leadId, array $statuses, array $createdTypes): int
    {
        return (int)Quote::find()
            ->andWhere(['lead_id' => $leadId])
            ->andWhere(['status' => $statuses])
            ->andWhere(['q_create_type_id' => $createdTypes])
            ->count();
    }

    public function getAmountQuoteByLeadIdAndStatuses(int $leadId, array $statuses): int
    {
        return (int)Quote::find()
            ->andWhere(['lead_id' => $leadId])
            ->andWhere(['status' => $statuses])
            ->count();
    }
}
